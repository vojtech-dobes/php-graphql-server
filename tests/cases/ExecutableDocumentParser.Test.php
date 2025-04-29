<?php declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';

/**
 * @dataProvider ExecutableDocumentParser.Test.cases.php
 */

$case = Tester\Environment::loadData();

[
	$schemaString,
	$documentText,
	$expectedErrors,
] = $case;

/** @var array<string, Vojtechdobes\GraphQL\ScalarImplementation<mixed, mixed>> $scalarImplementations */
$scalarImplementations = array_map(
	static fn ($scalarImplementationClass) => new $scalarImplementationClass(),
	$case[3] ?? [],
);


$schema = new Vojtechdobes\GraphQL\SchemaParser()
	->parseSchema(
		schemaString: $schemaString,
		enumClasses: [],
		scalarImplementations: $scalarImplementations,
	)
	->buildSchema();

$executableDocumentParser = new Vojtechdobes\GraphQL\ExecutableDocumentParser();

try {
	$executableDocumentParser
		->parseExecutableDocument($schema, $documentText)
		->buildExecutableDocument();

	Tester\Assert::same(
		expected: $expectedErrors,
		actual: [],
	);
} catch (Vojtechdobes\GraphQL\Exceptions\InvalidExecutableDocumentException $e) {
	Tester\Assert::same(
		expected: $expectedErrors,
		actual: array_map(
			static fn ($error) => $error->message,
			$e->errors,
		),
	);
}
