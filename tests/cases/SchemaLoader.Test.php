<?php declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';

/**
 * @dataProvider SchemaLoader.Test.cases.php
 */

$case = Tester\Environment::loadData();

[
	$schemaString,
	$abstractTypeNamesWithResolver,
	$fieldNamesWithResolver,
	$enumClasses,
	$scalarImplementations,
	$expectedErrors,
] = $case;

/** @var array<string, Vojtechdobes\GraphQL\ScalarImplementation<mixed, mixed>> $scalarImplementations */
$scalarImplementations = array_map(
	static fn ($scalarImplementationClass) => new $scalarImplementationClass(),
	$scalarImplementations,
);

$schemaFileHandle = tmpfile();
fwrite($schemaFileHandle, $schemaString);


$schemaLoader = new Vojtechdobes\GraphQL\SchemaLoader(
	autoReload: true,
	tempDir: getTempDirForTestCase(),
);

try {
	$schemaLoader->loadSchema(
		schemaPath: stream_get_meta_data($schemaFileHandle)['uri'],
		enumClasses: $enumClasses,
		scalarImplementations: $scalarImplementations,
		backendValidator: static fn () => [
			'abstractTypeNamesWithResolver' => $abstractTypeNamesWithResolver,
			'fieldNamesWithResolver' => $fieldNamesWithResolver,
		],
	);

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
