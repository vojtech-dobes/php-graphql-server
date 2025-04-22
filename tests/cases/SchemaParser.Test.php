<?php declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';

/**
 * @dataProvider SchemaParser.Test.cases.php
 */

$case = Tester\Environment::loadData();

[
	$schemaString,
	$expectedErrors,
] = $case;

/** @var array<string, Vojtechdobes\GraphQL\ScalarImplementation<mixed, mixed>> $scalarImplementations */
$scalarImplementations = array_map(
	static fn ($scalarImplementationClass) => new $scalarImplementationClass(),
	$case[2] ?? [],
);

$schemaParser = new Vojtechdobes\GraphQL\SchemaParser();

try {
	$schemaParser
		->parseSchema(
			schemaString: $schemaString,
			enumClasses: [],
			scalarImplementations: $scalarImplementations,
		)
		->buildSchema();

	Tester\Assert::same(
		expected: $expectedErrors,
		actual: [],
	);
} catch (Vojtechdobes\GraphQL\Exceptions\InvalidSchemaException $e) {
	Tester\Assert::same(
		expected: $expectedErrors,
		actual: $e->errors,
	);
}
