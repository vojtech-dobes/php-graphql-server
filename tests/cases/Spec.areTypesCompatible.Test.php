<?php declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';

/**
 * @dataProvider Spec.areTypesCompatible.Test.cases.php
 */

$case = Tester\Environment::loadData();

[
	$typeA,
	$typeB,
	$result,
] = $case;


$languageParser = new Vojtechdobes\GraphQL\Language\Parser();

Tester\Assert::same(
	expected: $result,
	actual: Vojtechdobes\GraphQL\Spec::areTypesCompatible(
		$languageParser->parseType($typeA),
		$languageParser->parseType($typeB),
	),
);
