<?php declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';


final class SilentErrorHandler implements Vojtechdobes\GraphQL\ErrorHandler
{

	public function handleFieldResolverError(Throwable $e, Vojtechdobes\GraphQL\FieldSelection $fieldSelection): void {}

	public function handleAbstractTypeResolverError(Throwable $e, Vojtechdobes\GraphQL\FieldSelection $fieldSelection, mixed $objectValue): void {}

	public function handleSerializeScalarError(Throwable $e, Vojtechdobes\GraphQL\FieldSelection $fieldSelection, mixed $scalarValue): void {}

}


$schema = new Vojtechdobes\GraphQL\SchemaParser()
	->parseSchema('type O implements I { a: String } interface I { a: String } type Query { a: I }', [], [])
	->buildSchema();

$document = new Vojtechdobes\GraphQL\ExecutableDocumentParser()
	->parseExecutableDocument($schema, 'fragment F on I { a } query { a { ...F } }')
	->buildExecutableDocument();

$executableSchema = new Vojtechdobes\GraphQL\ExecutableSchema(
	abstractTypeResolverProvider: new Vojtechdobes\GraphQL\StaticAbstractTypeResolverProvider([
		'I' => new Vojtechdobes\GraphQL\CallbackAbstractTypeResolver(
			static fn () => throw new Exception('Yikes!'),
		),
	]),
	contextFactory: new Vojtechdobes\GraphQL\NullContextFactory(),
	enableIntrospection: false,
	errorHandler: new SilentErrorHandler(),
	fieldResolverProvider: new Vojtechdobes\GraphQL\StaticFieldResolverProvider([
		'Query.a' => new Vojtechdobes\GraphQL\CallbackFieldResolver(
			static fn () => [],
		),
	]),
	schema: $schema,
);


$requestExecutor = new Vojtechdobes\GraphQL\RequestExecutor();

$result = $requestExecutor->executeRequest(
	$executableSchema,
	new Vojtechdobes\GraphQL\Request($document, null, []),
);

Tester\Assert::same(
	expected: [
		'data' => [
			'a' => null,
		],
		'errors' => [
			[
				'message' => 'Abstract type I failed to resolve',
				'path' => ['a'],
			],
		],
	],
	actual: $result->wait()->toResponse(),
);
