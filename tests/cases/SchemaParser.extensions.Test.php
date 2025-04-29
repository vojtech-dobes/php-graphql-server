<?php declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';

/**
 * @dataProvider SchemaParser.extensions.Test.cases.php
 */

$case = Tester\Environment::loadData();

[
	$schemaString,
	$schemaExtensionString,
	$expectedErrors,
] = $case;

/** @var array<string, Vojtechdobes\GraphQL\ScalarImplementation<mixed, mixed>> $scalarImplementations */
$scalarImplementations = array_map(
	static fn ($scalarImplementationClass) => new $scalarImplementationClass(),
	$case[3] ?? [],
);

$schemaParser = new Vojtechdobes\GraphQL\SchemaParser();

try {
	$schemaParser
		->parseSchema(
			schemaString: $schemaString,
			enumClasses: [],
			scalarImplementations: $scalarImplementations,
			schemaExtensionStrings: [$schemaExtensionString],
		)
		->buildSchema();

	Tester\Assert::same(
		expected: $expectedErrors,
		actual: [],
	);
} catch (Vojtechdobes\GraphQL\Exceptions\InvalidSchemaException $e) {
	Tester\Assert::same(
		expected: $expectedErrors,
		actual: array_map(
			static fn ($error) => $error->message,
			$e->errors,
		),
	);
}
