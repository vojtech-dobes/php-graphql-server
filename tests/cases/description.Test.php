<?php declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';

/**
 * @dataProvider description.Test.cases.php
 */

$case = Tester\Environment::loadData();

[
	$input,
	$expectedValue,
] = $case;


$languageParser = new Vojtechdobes\GraphQL\Language\Parser();

Tester\Assert::same(
	expected: $expectedValue,
	actual: $languageParser->parseDescription(trim($input)),
);
