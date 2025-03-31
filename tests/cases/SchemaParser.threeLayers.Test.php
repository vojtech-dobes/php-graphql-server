<?php declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';


$schemaString = 'schema { query: Query } type Query';
$schemaExtensionStrings = [
	'extend schema { mutation: Mutation } type Mutation',
	'extend schema { subscription: Subscription } type Subscription',
];


$schemaParser = new Vojtechdobes\GraphQL\SchemaParser();

Tester\Assert::noError(
	static fn () => $schemaParser
		->parseSchema(
			schemaString: $schemaString,
			enumClasses: [],
			scalarImplementations: [],
			schemaExtensionStrings: $schemaExtensionStrings,
		)
		->buildSchema(),
);
