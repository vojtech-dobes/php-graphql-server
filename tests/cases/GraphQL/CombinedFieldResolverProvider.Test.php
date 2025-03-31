<?php declare(strict_types=1);

require_once __DIR__ . '/../../bootstrap.php';


$fieldResolverA = new Vojtechdobes\GraphQL\CallbackFieldResolver(static fn () => 'Alice');
$fieldResolverB = new Vojtechdobes\GraphQL\CallbackFieldResolver(static fn () => 'Bob');

$combinedFieldResolverProvider = new Vojtechdobes\GraphQL\CombinedFieldResolverProvider([
	new Vojtechdobes\GraphQL\StaticFieldResolverProvider([
		'a' => $fieldResolverA,
	]),
	new Vojtechdobes\GraphQL\StaticFieldResolverProvider([
		'b' => $fieldResolverB,
	]),
]);


Tester\Assert::true(
	$combinedFieldResolverProvider->hasFieldResolver('a'),
);

Tester\Assert::true(
	$combinedFieldResolverProvider->hasFieldResolver('b'),
);

Tester\Assert::false(
	$combinedFieldResolverProvider->hasFieldResolver('c'),
);


Tester\Assert::same(
	$fieldResolverA,
	$combinedFieldResolverProvider->getFieldResolver('a'),
);

Tester\Assert::same(
	$fieldResolverB,
	$combinedFieldResolverProvider->getFieldResolver('b'),
);

Tester\Assert::null(
	$combinedFieldResolverProvider->getFieldResolver('c'),
);
