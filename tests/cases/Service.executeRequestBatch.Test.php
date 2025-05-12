<?php declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';


class Buffer
{

	/** @var list<string> */
	private static array $buffer = [];

	public static function add(string $item): void
	{
		self::$buffer[] = $item;
	}

	public static function export(): string
	{
		return implode('.', self::$buffer);
	}

}

/**
 * @return callable(mixed, Vojtechdobes\GraphQL\FieldSelection): Vojtechdobes\GraphQL\Deferred<string>
 */
function ResolveWithBufferAndVariable(): callable {
	return static function ($parent, Vojtechdobes\GraphQL\FieldSelection $fieldSelection): Vojtechdobes\GraphQL\Deferred {
		Buffer::add($fieldSelection->name . '+' . $fieldSelection->arguments['arg']);

		return new Vojtechdobes\GraphQL\Deferred(
			static fn () => Buffer::export(),
		);
	};
}


$schema = new Vojtechdobes\GraphQL\SchemaParser()
	->parseSchema('type Query { a(arg: String): String b(arg: String): String c(arg: String): String }', [], [])
	->buildSchema();

$requests = [
	new Vojtechdobes\GraphQL\Request(
		new Vojtechdobes\GraphQL\ExecutableDocumentParser()
			->parseExecutableDocument($schema, 'query Q($var: String) { a(arg: $var) }')
			->buildExecutableDocument(),
		'Q',
		[
			'var' => 'A',
		],
	),
	new Vojtechdobes\GraphQL\Request(
		new Vojtechdobes\GraphQL\ExecutableDocumentParser()
			->parseExecutableDocument($schema, 'query Q($var: String) { b(arg: $var) }')
			->buildExecutableDocument(),
		'Q',
		[
			'var' => 'B',
		],
	),
	new Vojtechdobes\GraphQL\Request(
		new Vojtechdobes\GraphQL\ExecutableDocumentParser()
			->parseExecutableDocument($schema, 'query Q($var: String) { c(arg: $var) }')
			->buildExecutableDocument(),
		'Q',
		[
			'var' => 'C',
		],
	),
];

$executableSchema = new Vojtechdobes\GraphQL\ExecutableSchema(
	abstractTypeResolverProvider: new Vojtechdobes\GraphQL\StaticAbstractTypeResolverProvider([]),
	contextFactory: new Vojtechdobes\GraphQL\NullContextFactory(),
	enableIntrospection: false,
	errorHandler: new Vojtechdobes\GraphQL\ThrowErrorHandler(),
	fieldResolverProvider: new Vojtechdobes\GraphQL\StaticFieldResolverProvider([
		'Query.a' => new Vojtechdobes\GraphQL\CallbackFieldResolver(ResolveWithBufferAndVariable()),
		'Query.b' => new Vojtechdobes\GraphQL\CallbackFieldResolver(ResolveWithBufferAndVariable()),
		'Query.c' => new Vojtechdobes\GraphQL\CallbackFieldResolver(ResolveWithBufferAndVariable()),
	]),
	schema: $schema,
);


$service = Vojtechdobes\GraphQL\ServiceFactory::createService($executableSchema);

$results = $service->executeRequestBatch($requests)->wait();

Tester\Assert::same(
	expected: [
		[
			'data' => [
				'a' => 'a+A.b+B.c+C',
			],
		],
		[
			'data' => [
				'b' => 'a+A.b+B.c+C',
			],
		],
		[
			'data' => [
				'c' => 'a+A.b+B.c+C',
			],
		],
	],
	actual: array_map(
		static fn ($result) => $result->toResponse(),
		$results,
	),
);
