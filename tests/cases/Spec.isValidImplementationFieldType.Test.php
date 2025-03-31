<?php declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';

/**
 * @dataProvider Spec.isValidImplementationFieldType.Test.cases.php
 */

$case = Tester\Environment::loadData();

[
	$schemaString,
	$typeA,
	$typeB,
	$result,
] = $case;

/** @var array<string, Vojtechdobes\GraphQL\ScalarImplementation<mixed, mixed>> $scalarImplementations */
$scalarImplementations = array_map(
	static fn ($scalarImplementationClass) => new $scalarImplementationClass(),
	$case[4] ?? [],
);


$schema = new Vojtechdobes\GraphQL\SchemaParser()
	->parseSchema(
		schemaString: $schemaString . ' type Query',
		enumClasses: [],
		scalarImplementations: $scalarImplementations,
	)
	->buildSchema();


$languageParser = new Vojtechdobes\GraphQL\Language\Parser();

Tester\Assert::same(
	expected: $result,
	actual: Vojtechdobes\GraphQL\Spec::isValidImplementationFieldType(
		$schema->typeDefinitionRegistry,
		$languageParser->parseType($typeA),
		$languageParser->parseType($typeB),
	),
);
