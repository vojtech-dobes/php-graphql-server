<?php declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';

/**
 * @dataProvider RequestExecutor.Test.cases.php
 */

$case = Tester\Environment::loadData();

[
	$schemaString,
	$resolvedValues,
	$documentText,
	$variables,
	$expectedResponse,
] = $case;


/** @var array<string, Vojtechdobes\GraphQL\ScalarImplementation<mixed, mixed>> $scalarImplementations */
$scalarImplementations = array_map(
	static fn ($scalarImplementationClass) => new $scalarImplementationClass(),
	$case[5] ?? [],
);


$schema = new Vojtechdobes\GraphQL\SchemaParser()
	->parseSchema(
		schemaString: $schemaString,
		enumClasses: [],
		scalarImplementations: $scalarImplementations,
	)
	->buildSchema();

$document = new Vojtechdobes\GraphQL\ExecutableDocumentParser()
	->parseExecutableDocument($schema, $documentText)
	->buildExecutableDocument();

if (isset($resolvedValues['abstractTypeResolvers'])) {
	$abstractTypeResolvers = array_map(
		static fn ($value) => new Vojtechdobes\GraphQL\CallbackAbstractTypeResolver(
			is_callable($value)
				? $value
				: static fn () => $value,
		),
		$resolvedValues['abstractTypeResolvers'],
	);

	unset($resolvedValues['abstractTypeResolvers']);
} else {
	$abstractTypeResolvers = [];
}

$fieldResolverProvider = new Vojtechdobes\GraphQL\StaticFieldResolverProvider(
	array_map(
		static fn ($value) => new Vojtechdobes\GraphQL\CallbackFieldResolver(
			is_callable($value)
				? $value
				: static fn () => $value,
		),
		$resolvedValues,
	),
);

$executableSchema = new Vojtechdobes\GraphQL\ExecutableSchema(
	abstractTypeResolverProvider: new Vojtechdobes\GraphQL\StaticAbstractTypeResolverProvider($abstractTypeResolvers),
	contextFactory: new Vojtechdobes\GraphQL\NullContextFactory(),
	enableIntrospection: true,
	errorHandler: new Vojtechdobes\GraphQL\ThrowErrorHandler(),
	fieldResolverProvider: $fieldResolverProvider,
	schema: $schema,
);


$requestExecutor = new Vojtechdobes\GraphQL\RequestExecutor();

$result = $requestExecutor->executeRequest(
	$executableSchema,
	new Vojtechdobes\GraphQL\Request($document, $variables),
);

Tester\Assert::equal(
	expected: $expectedResponse,
	actual: $result->wait()->toResponse(),
	matchOrder: true,
);
