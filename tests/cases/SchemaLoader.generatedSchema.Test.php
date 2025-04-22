<?php declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';


$schemaLoader = new Vojtechdobes\GraphQL\SchemaLoader(
	autoReload: false,
	tempDir: getTempDirForTestCase(),
);

$schemaFileHandle = tmpfile();
$schemaFilePath = stream_get_meta_data($schemaFileHandle)['uri'];
fwrite($schemaFileHandle, '
	directive @d repeatable on OBJECT
	type A @d { a: String }
	type Query { a: A }
');

$schema = $schemaLoader->loadSchema(
	schemaPath: $schemaFilePath,
	enumClasses: [],
	scalarImplementations: [],
);

Tester\Assert::notNull(
	$schema->getTypeDefinitionOrNull('A'),
);

Tester\Assert::type(
	Vojtechdobes\GraphQL\TypeSystem\ObjectTypeDefinition::class,
	$schema->getTypeDefinition('A'),
);

Tester\Assert::same(
	[
		'Boolean',
		'Float',
		'ID',
		'Int',
		'String',
		'__Schema',
		'__Type',
		'__TypeKind',
		'__Field',
		'__InputValue',
		'__EnumValue',
		'__Directive',
		'__DirectiveLocation',
		'A',
		'Query',
	],
	array_keys($schema->getTypeDefinitions()),
);

Tester\Assert::notNull(
	$schema->getDirectiveDefinitionOrNull('d'),
);

Tester\Assert::true(
	$schema->getDirectiveDefinition('d')->isRepeatable,
);


// autoReload = false should ignore any change in the schema file
file_put_contents($schemaFilePath, '');

$schema = $schemaLoader->loadSchema(
	schemaPath: $schemaFilePath,
	enumClasses: [],
	scalarImplementations: [],
);

Tester\Assert::notNull(
	$schema->getTypeDefinitionOrNull('A'),
);

Tester\Assert::notNull(
	$schema->getDirectiveDefinitionOrNull('d'),
);
