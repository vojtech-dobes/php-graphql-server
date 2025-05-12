<?php declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';


enum E: string
{

	case M = 'M';

	public function toLabel(): string
	{
		return match ($this) {
			self::M => 'Matthew',
		};
	}

}


$schema = new Vojtechdobes\GraphQL\SchemaParser()
	->parseSchema('enum E { M } type Query { a(arg1: E!): String }', ['E' => E::class], [])
	->buildSchema();

$document = new Vojtechdobes\GraphQL\ExecutableDocumentParser()
	->parseExecutableDocument($schema, 'query Q($var1: E!) { a(arg1: $var1) }')
	->buildExecutableDocument();

$executableSchema = new Vojtechdobes\GraphQL\ExecutableSchema(
	abstractTypeResolverProvider: new Vojtechdobes\GraphQL\StaticAbstractTypeResolverProvider([]),
	contextFactory: new Vojtechdobes\GraphQL\NullContextFactory(),
	enableIntrospection: false,
	errorHandler: new Vojtechdobes\GraphQL\ThrowErrorHandler(),
	fieldResolverProvider: new Vojtechdobes\GraphQL\StaticFieldResolverProvider([
		'Query.a' => new Vojtechdobes\GraphQL\CallbackFieldResolver(
			static fn ($objectValue, $fieldSelection) => $fieldSelection->arguments['arg1']->toLabel(),
		),
	]),
	schema: $schema,
);


$requestExecutor = new Vojtechdobes\GraphQL\RequestExecutor();

$result = $requestExecutor->executeRequest(
	$executableSchema,
	new Vojtechdobes\GraphQL\Request($document, 'Q', ['var1' => 'M']),
);

Tester\Assert::same(
	expected: [
		'data' => [
			'a' => 'Matthew',
		],
	],
	actual: $result->wait()->toResponse(),
);
