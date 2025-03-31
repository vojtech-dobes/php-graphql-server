<?php declare(strict_types=1);

require_once __DIR__ . '/../../bootstrap.php';

Tester\Environment::setupFunctions();


test('Error with message', function () {
	$error = new Vojtechdobes\GraphQL\Error(
		'Something bad happened',
	);

	Tester\Assert::same(
		[
			'message' => 'Something bad happened',
		],
		$error->toResponse(),
	);
});


test('Error with message and path', function () {
	$error = new Vojtechdobes\GraphQL\Error(
		'Something bad happened',
		Vojtechdobes\GraphQL\Execution\ResponsePath::createBasePath()
			->addField('a')
			->addField('b')
			->addField('c'),
	);

	Tester\Assert::same(
		[
			'message' => 'Something bad happened',
			'path' => ['a', 'b', 'c'],
		],
		$error->toResponse(),
	);
});


test('Error with message and path and extensions', function () {
	$error = new Vojtechdobes\GraphQL\Error(
		'Something bad happened',
		Vojtechdobes\GraphQL\Execution\ResponsePath::createBasePath()
			->addField('a')
			->addField('b')
			->addField('c'),
		[
			'CODE' => 'X',
		],
	);

	Tester\Assert::same(
		[
			'message' => 'Something bad happened',
			'path' => ['a', 'b', 'c'],
			'extensions' => [
				'CODE' => 'X',
			],
		],
		$error->toResponse(),
	);
});


test('Error with message and extensions', function () {
	$error = new Vojtechdobes\GraphQL\Error(
		'Something bad happened',
		null,
		[
			'CODE' => 'X',
		],
	);

	Tester\Assert::same(
		[
			'message' => 'Something bad happened',
			'extensions' => [
				'CODE' => 'X',
			],
		],
		$error->toResponse(),
	);
});


test('Error with message and path being added later', function () {
	$error = new Vojtechdobes\GraphQL\Error(
		'Something bad happened',
	)->withResponsePath(
		Vojtechdobes\GraphQL\Execution\ResponsePath::createBasePath()
			->addField('a')
			->addField('b')
			->addField('c'),
	);

	Tester\Assert::same(
		[
			'message' => 'Something bad happened',
			'path' => ['a', 'b', 'c'],
		],
		$error->toResponse(),
	);
});
