<?php declare(strict_types=1);

if (function_exists('RootQueryType') === false) {
	function RootQueryType(): string { return '   ""   type Query'; }
}

return [
	// schema
	'valid schema extension' => [
		'schema { query: Query } type Query',
		'extend schema { mutation: Mutation } type Mutation',
		[],
	],
	'invalid overriding of schema' => [
		'schema { query: Query } type Query',
		'schema { query: Query }',
		[
			"Schema extension can't define 'schema' block because it's already defined in base schema",
		],
	],
	'invalid schema definition & extension at the same time' => [
		RootQueryType(),
		'schema { query: Query } extend schema { query: Query }',
		[
			"Schema definition can't be defined and extended at the same time",
		],
	],
	'invalid extension of missing schema' => [
		RootQueryType(),
		'extend schema { query: Query }',
		[
			"Schema definition can't be extended because it's not defined",
		],
	],
	'invalid duplicate schema extension' => [
		'schema { query: Query } type Query',
		'extend schema { query: Query } extend schema { query: Query }',
		[
			"Schema definition can't be extended multiple times",
		],
	],
	'invalid schema extension with unknown operation' => [
		'schema { query: Query } type Query',
		'extend schema { x: Query }',
		[
			"Expected token 'subscription', got 'x' instead (line 1, col 17)",
		],
	],
	'invalid schema extension with duplicate operation type' => [
		'schema { query: Query } type Query',
		'extend schema { mutation: A mutation: B } type A type B',
		[
			"Schema definition can't be extended to define root operation type 'mutation' multiple times",
		],
	],
	'invalid schema extension with unknown operation type' => [
		'schema { query: Query } type Query',
		'extend schema { mutation: A }',
		[
			"Mutation root operation has unknown type 'A'",
		],
	],
	'invalid schema with non-object operation type' => [
		'schema { query: Query } type Query',
		'extend schema { mutation: A } enum A',
		[
			"Mutation root operation must have object type, but enum type 'A' given",
		],
	],
	'invalid schema with enum operation type' => [
		'schema { query: Query } type Query',
		'extend schema { mutation: A } enum A',
		[
			"Mutation root operation must have object type, but enum type 'A' given",
		],
	],
	'invalid schema with input operation type' => [
		'schema { query: Query } type Query',
		'extend schema { mutation: A } input A',
		[
			"Mutation root operation must have object type, but input object type 'A' given",
		],
	],
	'invalid schema with interface operation type' => [
		'schema { query: Query } type Query',
		'extend schema { mutation: A } interface A',
		[
			"Mutation root operation must have object type, but interface type 'A' given",
		],
	],
	'invalid schema with scalar operation type' => [
		'schema { query: Query } type Query',
		'extend schema { mutation: A } scalar A',
		[
			"Mutation root operation must have object type, but scalar type 'A' given",
		],
		[
			'A' => Vojtechdobes\GraphQL\Builtin\StringScalarImplementation::class,
		],
	],
	'invalid schema with union operation type' => [
		'schema { query: Query } type Query',
		'extend schema { mutation: A } union A',
		[
			"Mutation root operation must have object type, but union type 'A' given",
		],
	],
	'invalid schema extension with unknown directive' => [
		'schema { query: Query } type Query',
		'extend schema @d',
		[
			"Schema definition can't be extended with unknown directive @d",
		],
	],
	'invalid schema extension with non-union directive' => [
		'schema { query: Query } type Query',
		'directive @d on SCALAR extend schema @d',
		[
			"Directive @d isn't allowed to be placed on schema definition",
		],
	],
	'invalid schema extension with repeated non-repeatable directive' => [
		'schema { query: Query } type Query',
		'directive @d on SCHEMA extend schema @d @d',
		[
			"Directive @d on schema definition can't be repeated",
		],
	],
	'invalid schema extension with already configured non-repeatable directive' => [
		'directive @d on SCHEMA schema @d { query: Query } type Query',
		'extend schema @d',
		[
			"Schema definition can't be extended with already configured non-repeatable directive @d",
		],
	],
	// general type
	'invalid type extension of unknown type' => [
		RootQueryType(),
		'extend type A { a: String }',
		[
			"Type 'A' can't be extended because it's not defined",
		],
	],
	// enum
	'valid enum type extension' => [
		'directive @d repeatable on ENUM enum E @d { M N }' . RootQueryType(),
		'directive @e on ENUM extend enum E @d @e { O }',
		[],
	],
	'invalid enum type definition & extension at the same time' => [
		'enum E { M N } extend enum E { O }' . RootQueryType(),
		'schema { query: Query }',
		[
			"Type 'E' can't be defined and extended at the same time",
		],
	],
	'invalid duplicate enum type extension' => [
		'enum E { M N }' . RootQueryType(),
		'extend enum E { O } extend enum E { P }',
		[
			"Type 'E' can't be extended multiple times",
		],
	],
	'invalid enum type extension of non-enum type' => [
		'type A' . RootQueryType(),
		'extend enum A { O }',
		[
			"Object type 'A' can't be extended as enum",
		],
	],
	'invalid enum type extension with duplicate values' => [
		'enum E { M N }' . RootQueryType(),
		'extend enum E { O O }',
		[
			"Enum type 'E' can't be extended with value 'O' multiple times",
		],
	],
	'invalid enum type extension that defines already existing value' => [
		'enum E { M N }' . RootQueryType(),
		'extend enum E { M }',
		[
			"Enum type 'E' can't be extended with already defined value 'M'",
		],
	],
	'invalid enum type extension with value with 2-underscore name prefix' => [
		'enum E { M N }' . RootQueryType(),
		'extend enum E { __O }',
		[
			"Enum type 'E' value name '__O' can't begin with '__', because such names are reserved for introspection",
		],
	],
	'invalid enum type extension with unknown directive' => [
		'enum E { M N }' . RootQueryType(),
		'extend enum E @d',
		[
			"Enum type 'E' can't be extended with unknown directive @d",
		],
	],
	'invalid enum type extension with non-enum directive' => [
		'enum E { M N }' . RootQueryType(),
		'directive @d on SCALAR extend enum E @d',
		[
			"Directive @d isn't allowed to be placed on enum type 'E'",
		],
	],
	'invalid enum type extension with repeated non-repeatable directive' => [
		'enum E { M N }' . RootQueryType(),
		'directive @d on ENUM extend enum E @d @d',
		[
			"Directive @d on enum type 'E' can't be repeated",
		],
	],
	'invalid enum type extension with already configured non-repeatable directive' => [
		'directive @d on ENUM enum E @d { M N }' . RootQueryType(),
		'extend enum E @d',
		[
			"Enum type 'E' can't be extended with already configured non-repeatable directive @d",
		],
	],
	'invalid enum type extension with value with unknown directive' => [
		'enum E { M N }' . RootQueryType(),
		'extend enum E { O @d }',
		[
			"Directive @d on enum type 'E' value 'O' isn't defined",
		],
	],
	// input object
	'valid input object type extension' => [
		'input IO { a: String }' . RootQueryType(),
		'extend input IO { b: String }',
		[],
	],
	'invalid input object type definition & extension at the same time' => [
		'input IO { a: String } extend input IO { b: String }' . RootQueryType(),
		'schema { query: Query }',
		[
			"Type 'IO' can't be defined and extended at the same time",
		],
	],
	'invalid duplicate input object type extension' => [
		'input IO { a: String }' . RootQueryType(),
		'extend input IO { b: String } extend input IO { b: String }',
		[
			"Type 'IO' can't be extended multiple times",
		],
	],
	'invalid input object type extension of non-input object type' => [
		'type A' . RootQueryType(),
		'extend input A { b: String }',
		[
			"Object type 'A' can't be extended as input object",
		],
	],
	'invalid input object type extension with duplicate fields' => [
		'input IO' . RootQueryType(),
		'extend input IO { a: String a: String }',
		[
			"Input object type 'IO' can't be extended to define field 'a' multiple times",
		],
	],
	'invalid input object type extension that defines already defined field' => [
		'input IO { a: String }' . RootQueryType(),
		'extend input IO { a: String }',
		[
			"Input object type 'IO' can't be extended to define already defined field 'a'",
		],
	],
	'invalid input object type extension with field with 2-underscore name prefix' => [
		'input IO' . RootQueryType(),
		'extend input IO { __a: String }',
		[
			"Input object type 'IO' extended field name '__a' can't begin with '__', because such names are reserved for introspection",
		],
	],
	'invalid input object type extension with field with unknown type' => [
		'input IO' . RootQueryType(),
		'extend input IO { a: A }',
		[
			"Extended input object field 'IO.a' has unknown type 'A'",
		],
	],
	'invalid input object type extension with field with interface type' => [
		'input IO' . RootQueryType(),
		'interface A extend input IO { a: A }',
		[
			"Extended input object field 'IO.a' must have input type, but interface type 'A' given",
		],
	],
	'invalid input object type extension with field with object type' => [
		'input IO' . RootQueryType(),
		'type A extend input IO { a: A }',
		[
			"Extended input object field 'IO.a' must have input type, but object type 'A' given",
		],
	],
	'invalid input object type extension with field with union type' => [
		'input IO' . RootQueryType(),
		'union A extend input IO { a: A }',
		[
			"Extended input object field 'IO.a' must have input type, but union type 'A' given",
		],
	],
	'invalid input object type with single direct non-nullable circular reference' => [
		'input IO' . RootQueryType(),
		'extend input IO { a: IO! }',
		[
			"Extended input object type 'IO' can't reference itself in field 'a' by a chain of non-nullable fields",
		],
	],
	'invalid input object type with indirect non-nullable circular reference' => [
		'input IO' . RootQueryType(),
		'input A { b: IO! } extend input IO { a: A! }',
		[
			"Extended input object type 'IO' can't reference itself in field 'a' by a chain of non-nullable fields",
			"Input object type 'A' can't reference itself in field 'b' by a chain of non-nullable fields",
		],
	],
	'invalid input object type with indirect non-nullable circular reference through new type' => [
		'input IO' . RootQueryType(),
		'input A { b: IO! } extend input IO { a: A! }',
		[
			"Extended input object type 'IO' can't reference itself in field 'a' by a chain of non-nullable fields",
			"Input object type 'A' can't reference itself in field 'b' by a chain of non-nullable fields",
		],
	],
	'invalid input object type with indirect non-nullable circular reference through other extended type' => [
		'input A input B' . RootQueryType(),
		'extend input A { b: B! } extend input B { a: A! }',
		[
			"Extended input object type 'A' can't reference itself in field 'b' by a chain of non-nullable fields",
			"Extended input object type 'B' can't reference itself in field 'a' by a chain of non-nullable fields",
		],
	],
	'invalid input object type extension with field with incompatible default value' => [
		'input IO' . RootQueryType(),
		'extend input IO { a: String = true }',
		[
			"Default value of extended input object field 'IO.a' must be scalar 'String', but boolean value given",
		],
	],
	'invalid input object type extension with field with unknown directive' => [
		'input IO' . RootQueryType(),
		'extend input IO { a: String @d }',
		[
			"Directive @d on extended input object field 'IO.a' isn't defined",
		],
	],
	'invalid input object type extension with field with unsupported directive' => [
		'input IO' . RootQueryType(),
		'directive @d on ENUM extend input IO { a: String @d }',
		[
			"Directive @d isn't allowed to be placed on extended input object field 'IO.a'",
		],
	],
	'invalid input object type extension with unknown directive' => [
		'input IO' . RootQueryType(),
		'extend input IO @d',
		[
			"Input object type 'IO' can't be extended with unknown directive @d",
		],
	],
	'invalid input object type extension with non-input object directive' => [
		'input IO' . RootQueryType(),
		'directive @d on SCALAR extend input IO @d',
		[
			"Directive @d isn't allowed to be placed on input object type 'IO'",
		],
	],
	'invalid input object type extension with repeated non-repeatable directive' => [
		'input IO' . RootQueryType(),
		'directive @d on INPUT_OBJECT extend input IO @d @d',
		[
			"Directive @d on input object type 'IO' can't be repeated",
		],
	],
	'invalid input object type extension with already configured non-repeatable directive' => [
		'directive @d on INPUT_OBJECT input IO @d' . RootQueryType(),
		'extend input IO @d',
		[
			"Input object type 'IO' can't be extended with already configured non-repeatable directive @d",
		],
	],
	// interface
	'valid interface type extension' => [
		'interface I { a: String }' . RootQueryType(),
		'extend interface I { b: String }',
		[],
	],
	'invalid interface type definition & extension at the same time' => [
		'interface I { a: String } extend interface I { b: String }' . RootQueryType(),
		'schema { query: Query }',
		[
			"Type 'I' can't be defined and extended at the same time",
		],
	],
	'invalid duplicate interface type extension' => [
		'interface I { a: String }' . RootQueryType(),
		'extend interface I { b: String } extend interface I { b: String }',
		[
			"Type 'I' can't be extended multiple times",
		],
	],
	'invalid interface type extension of non-interface type' => [
		'type A' . RootQueryType(),
		'extend interface A { b: String }',
		[
			"Object type 'A' can't be extended as interface",
		],
	],
	'invalid interface type extension that implements unknown interface' => [
		'interface I' . RootQueryType(),
		'extend interface I implements J',
		[
			"Interface type 'I' can't be extended to implement unknown type 'J'",
		],
	],
	'invalid interface type extension that implements enum type' => [
		'interface I' . RootQueryType(),
		'enum E extend interface I implements E',
		[
			"Interface type 'I' can't be extended to implement enum type 'E'",
		],
	],
	'invalid interface type extension that implements input object type' => [
		'interface I' . RootQueryType(),
		'input IO extend interface I implements IO',
		[
			"Interface type 'I' can't be extended to implement input object type 'IO'",
		],
	],
	'invalid interface type extension that implements object type' => [
		'interface I' . RootQueryType(),
		'type O extend interface I implements O',
		[
			"Interface type 'I' can't be extended to implement object type 'O'",
		],
	],
	'invalid interface type extension that implements scalar type' => [
		'interface I' . RootQueryType(),
		'scalar S extend interface I implements S',
		[
			"Interface type 'I' can't be extended to implement scalar type 'S'",
		],
		[
			'S' => Vojtechdobes\GraphQL\Builtin\StringScalarImplementation::class,
		],
	],
	'invalid interface type extension that implements union type' => [
		'interface I' . RootQueryType(),
		'union U extend interface I implements U',
		[
			"Interface type 'I' can't be extended to implement union type 'U'",
		],
	],
	'invalid interface type extension that implements itself directly' => [
		'interface I' . RootQueryType(),
		'extend interface I implements I',
		[
			"Interface type 'I' can't be extended to implement itself directly or indirectly",
		],
	],
	'invalid interface type extension that implements itself indirectly through new type' => [
		'interface I' . RootQueryType(),
		'interface J implements I extend interface I implements J',
		[
			"Interface type 'I' can't be extended to implement itself directly or indirectly",
			"Interface type 'J' can't implement itself directly or indirectly",
		],
	],
	'invalid interface type extension that implements itself indirectly through other extended type' => [
		'interface I interface J' . RootQueryType(),
		'extend interface I implements J extend interface J implements I',
		[
			"Interface type 'I' can't be extended to implement itself directly or indirectly",
			"Interface type 'J' can't be extended to implement itself directly or indirectly",
		],
	],
	'invalid interface type extension that does not implement transitive interface through new type' => [
		'interface I' . RootQueryType(),
		'interface J implements K interface K extend interface I implements J',
		[
			"Interface type 'I' must be also extended to directly implement interface 'K' because implemented interface 'J' implements it",
		],
	],
	'invalid interface type extension that does not implement transitive interface through other extended type' => [
		'interface I interface J' . RootQueryType(),
		'interface K extend interface I implements J extend interface J implements K',
		[
			"Interface type 'I' must be also extended to directly implement interface 'K' because implemented interface 'J' implements it",
		],
	],
	'invalid interface type that does not implement transitive extended interface' => [
		'interface J' . RootQueryType(),
		'interface I implements J interface K extend interface J implements K',
		[
			"Interface type 'I' must directly implement interface 'K' because implemented interface 'J' implements it",
		],
	],
	'invalid interface type extension with field that is already defined' => [
		'interface I { a: String }' . RootQueryType(),
		'extend interface I { a: String }',
		[
			"Interface type 'I' can't be extended to override already defined field 'a'",
		],
	],
	'invalid interface type extension with field with 2-underscore name prefix' => [
		'interface I' . RootQueryType(),
		'extend interface I { __a: String }',
		[
			"Interface type 'I' field name '__a' can't begin with '__', because such names are reserved for introspection",
		],
	],
	'invalid interface type extension with duplicate field' => [
		'interface I' . RootQueryType(),
		'extend interface I { a: String a: String }',
		[
			"Interface type 'I' can't be extended to define field 'a' multiple times",
		],
	],
	'invalid interface type extension with field with unknown type' => [
		'interface I' . RootQueryType(),
		'extend interface I { a: A }',
		[
			"Extended interface type field 'I.a' has unknown type 'A'",
		],
	],
	'invalid interface type extension with field with input object type' => [
		'interface I' . RootQueryType(),
		'input IO extend interface I { a: IO }',
		[
			"Extended interface type field 'I.a' must have output type, but input object type 'IO' given",
		],
	],
	'invalid interface type extension with field with unknown directive' => [
		'interface I' . RootQueryType(),
		'extend interface I { a: String @d }',
		[
			"Directive @d on extended interface type field 'I.a' isn't defined",
		],
	],
	'invalid interface type extension with field with non-field directive' => [
		'interface I' . RootQueryType(),
		'directive @d on SCALAR extend interface I { a: String @d }',
		[
			"Directive @d isn't allowed to be placed on extended interface type field 'I.a'",
		],
	],
	'invalid interface type extension with field with repeated non-repeatable directive' => [
		'interface I' . RootQueryType(),
		'directive @d on FIELD_DEFINITION extend interface I { a: String @d @d }',
		[
			"Directive @d on extended interface type field 'I.a' can't be repeated",
		],
	],
	'invalid interface type extension with unknown directive' => [
		'interface I' . RootQueryType(),
		'extend interface I @d',
		[
			"Interface type 'I' can't be extended with unknown directive @d",
		],
	],
	'invalid interface type extension with non-interface directive' => [
		'interface I' . RootQueryType(),
		'directive @d on SCALAR extend interface I @d',
		[
			"Directive @d isn't allowed to be placed on interface type 'I'",
		],
	],
	'invalid interface type extension with repeated non-repeatable directive' => [
		'interface I' . RootQueryType(),
		'directive @d on INTERFACE extend interface I @d @d',
		[
			"Directive @d on interface type 'I' can't be repeated",
		],
	],
	'invalid interface type extension with already configured non-repeatable directive' => [
		'directive @d on INTERFACE interface I @d' . RootQueryType(),
		'extend interface I @d',
		[
			"Interface type 'I' can't be extended with already configured non-repeatable directive @d",
		],
	],
	// object
	'valid object type extension' => [
		'type O { a: String }' . RootQueryType(),
		'extend type O { b: String }',
		[],
	],
	'invalid object type definition & extension at the same time' => [
		'type O { a: String } extend type O { b: String }' . RootQueryType(),
		'schema { query: Query }',
		[
			"Type 'O' can't be defined and extended at the same time",
		],
	],
	'invalid duplicate object type extension' => [
		'type O { a: String }' . RootQueryType(),
		'extend type O { b: String } extend type O { b: String }',
		[
			"Type 'O' can't be extended multiple times",
		],
	],
	'invalid object type extension of non-object type' => [
		'enum E' . RootQueryType(),
		'extend type E { b: String }',
		[
			"Enum type 'E' can't be extended as object",
		],
	],
	'invalid object type extension that implements unknown interface' => [
		'type O' . RootQueryType(),
		'extend type O implements J',
		[
			"Object type 'O' can't be extended to implement unknown type 'J'",
		],
	],
	'invalid object type extension that implements enum type' => [
		'type O' . RootQueryType(),
		'enum E extend type O implements E',
		[
			"Object type 'O' can't be extended to implement enum type 'E'",
		],
	],
	'invalid object type extension that implements input object type' => [
		'type O' . RootQueryType(),
		'input IO extend type O implements IO',
		[
			"Object type 'O' can't be extended to implement input object type 'IO'",
		],
	],
	'invalid object type extension that implements object type' => [
		'type O' . RootQueryType(),
		'type P extend type O implements P',
		[
			"Object type 'O' can't be extended to implement object type 'P'",
		],
	],
	'invalid object type extension that implements scalar type' => [
		'type O' . RootQueryType(),
		'scalar S extend type O implements S',
		[
			"Object type 'O' can't be extended to implement scalar type 'S'",
		],
		[
			'S' => Vojtechdobes\GraphQL\Builtin\StringScalarImplementation::class,
		],
	],
	'invalid object type extension that implements union type' => [
		'type O' . RootQueryType(),
		'union U extend type O implements U',
		[
			"Object type 'O' can't be extended to implement union type 'U'",
		],
	],
	'invalid object type extension that does not implement transitive interface through new type' => [
		'type O' . RootQueryType(),
		'interface I implements J interface J extend type O implements I',
		[
			"Object type 'O' must be also extended to directly implement interface 'J' because implemented interface 'I' implements it",
		],
	],
	'invalid object type extension that does not implement transitive interface through other extended type' => [
		'type O interface I' . RootQueryType(),
		'interface J extend type O implements I extend interface I implements J',
		[
			"Object type 'O' must be also extended to directly implement interface 'J' because implemented interface 'I' implements it",
		],
	],
	'invalid object type that does not implement transitive extended interface' => [
		'interface I' . RootQueryType(),
		'type O implements I interface J extend interface I implements J',
		[
			"Object type 'O' must directly implement interface 'J' because implemented interface 'I' implements it",
		],
	],
	'invalid object type extension with field that is already defined' => [
		'type O { a: String }' . RootQueryType(),
		'extend type O { a: String }',
		[
			"Object type 'O' can't be extended to override already defined field 'a'",
		],
	],
	'invalid object type extension with field with 2-underscore name prefix' => [
		'type O' . RootQueryType(),
		'extend type O { __a: String }',
		[
			"Object type 'O' field name '__a' can't begin with '__', because such names are reserved for introspection",
		],
	],
	'invalid object type extension with duplicate field' => [
		'type O' . RootQueryType(),
		'extend type O { a: String a: String }',
		[
			"Object type 'O' can't be extended to define field 'a' multiple times",
		],
	],
	'invalid object type extension with field with unknown type' => [
		'type O' . RootQueryType(),
		'extend type O { a: A }',
		[
			"Extended object type field 'O.a' has unknown type 'A'",
		],
	],
	'invalid object type extension with field with input object type' => [
		'type O' . RootQueryType(),
		'input IO extend type O { a: IO }',
		[
			"Extended object type field 'O.a' must have output type, but input object type 'IO' given",
		],
	],
	'invalid object type extension with field with unknown directive' => [
		'type O' . RootQueryType(),
		'extend type O { a: String @d }',
		[
			"Directive @d on extended object type field 'O.a' isn't defined",
		],
	],
	'invalid object type extension with field with non-field directive' => [
		'type O' . RootQueryType(),
		'directive @d on SCALAR extend type O { a: String @d }',
		[
			"Directive @d isn't allowed to be placed on extended object type field 'O.a'",
		],
	],
	'invalid object type extension with field with repeated non-repeatable directive' => [
		'type O' . RootQueryType(),
		'directive @d on FIELD_DEFINITION extend type O { a: String @d @d }',
		[
			"Directive @d on extended object type field 'O.a' can't be repeated",
		],
	],
	'invalid object type extension with unknown directive' => [
		'type O' . RootQueryType(),
		'extend type O @d',
		[
			"Object type 'O' can't be extended with unknown directive @d",
		],
	],
	'invalid object type extension with non-object directive' => [
		'type O' . RootQueryType(),
		'directive @d on SCALAR extend type O @d',
		[
			"Directive @d isn't allowed to be placed on object type 'O'",
		],
	],
	'invalid object type extension with repeated non-repeatable directive' => [
		'type O' . RootQueryType(),
		'directive @d on OBJECT extend type O @d @d',
		[
			"Directive @d on object type 'O' can't be repeated",
		],
	],
	'invalid object type extension with already configured non-repeatable directive' => [
		'directive @d on OBJECT type O @d' . RootQueryType(),
		'extend type O @d',
		[
			"Object type 'O' can't be extended with already configured non-repeatable directive @d",
		],
	],
	// scalar
	'valid scalar type extension' => [
		'scalar S' . RootQueryType(),
		'directive @d on SCALAR extend scalar S @d',
		[],
		[
			'S' => Vojtechdobes\GraphQL\Builtin\StringScalarImplementation::class,
		],
	],
	'invalid scalar type definition & extension at the same time' => [
		'scalar S directive @d on SCALAR extend scalar S @d' . RootQueryType(),
		'schema { query: Query }',
		[
			"Type 'S' can't be defined and extended at the same time",
		],
		[
			'S' => Vojtechdobes\GraphQL\Builtin\StringScalarImplementation::class,
		],
	],
	'invalid duplicate scalar type extension' => [
		'scalar S' . RootQueryType(),
		'directive @d on SCALAR extend scalar S @d extend scalar S @d',
		[
			"Type 'S' can't be extended multiple times",
		],
		[
			'S' => Vojtechdobes\GraphQL\Builtin\StringScalarImplementation::class,
		],
	],
	'invalid scalar type extension of non-scalar type' => [
		'type A' . RootQueryType(),
		'directive @d on SCALAR extend scalar A @d',
		[
			"Object type 'A' can't be extended as scalar",
		],
	],
	'invalid scalar type extension with unknown directive' => [
		'scalar S' . RootQueryType(),
		'extend scalar S @d',
		[
			"Scalar type 'S' can't be extended with unknown directive @d",
		],
		[
			'S' => Vojtechdobes\GraphQL\Builtin\StringScalarImplementation::class,
		],
	],
	'invalid scalar type extension with non-union directive' => [
		'scalar S' . RootQueryType(),
		'directive @d on ENUM extend scalar S @d',
		[
			"Directive @d isn't allowed to be placed on scalar type 'S'",
		],
		[
			'S' => Vojtechdobes\GraphQL\Builtin\StringScalarImplementation::class,
		],
	],
	'invalid scalar type extension with repeated non-repeatable directive' => [
		'scalar S' . RootQueryType(),
		'directive @d on SCALAR extend scalar S @d @d',
		[
			"Directive @d on scalar type 'S' can't be repeated",
		],
		[
			'S' => Vojtechdobes\GraphQL\Builtin\StringScalarImplementation::class,
		],
	],
	'invalid scalar type extension with already configured non-repeatable directive' => [
		'directive @d on SCALAR scalar S @d' . RootQueryType(),
		'extend scalar S @d',
		[
			"Scalar type 'S' can't be extended with already configured non-repeatable directive @d",
		],
		[
			'S' => Vojtechdobes\GraphQL\Builtin\StringScalarImplementation::class,
		],
	],
	// union
	'valid union type extension' => [
		'union U' . RootQueryType(),
		'type A extend union U = A',
		[],
	],
	'invalid union type definition & extension at the same time' => [
		'union U type A extend union U = A' . RootQueryType(),
		'schema { query: Query }',
		[
			"Type 'U' can't be defined and extended at the same time",
		],
	],
	'invalid duplicate union type extension' => [
		'union U' . RootQueryType(),
		'type A extend union U = A extend union U = A',
		[
			"Type 'U' can't be extended multiple times",
		],
	],
	'invalid union type extension of non-union type' => [
		'type A' . RootQueryType(),
		'type B extend union A = B',
		[
			"Object type 'A' can't be extended as union",
		],
	],
	'invalid union type extension with duplicate members' => [
		'union U' . RootQueryType(),
		'type A extend union U = A | A',
		[
			"Union type 'U' can't be extended to include type 'A' multiple times",
		],
	],
	'invalid union type extension that includes already included type' => [
		'type A union U = A' . RootQueryType(),
		'extend union U = A',
		[
			"Union type 'U' can't be extended to include already included type 'A'",
		],
	],
	'invalid union type extension that includes unknown type' => [
		'union U' . RootQueryType(),
		'extend union U = A',
		[
			"Union type 'U' can't be extended to include unknown type 'A'",
		],
	],
	'invalid union type extension that includes non-object type' => [
		'union U' . RootQueryType(),
		'enum E extend union U = E',
		[
			"Union type 'U' can't be extended to include enum type 'E'",
		],
	],
	'invalid union type extension with unknown directive' => [
		'union U' . RootQueryType(),
		'extend union U @d',
		[
			"Union type 'U' can't be extended with unknown directive @d",
		],
	],
	'invalid union type extension with non-union directive' => [
		'union U' . RootQueryType(),
		'directive @d on SCALAR extend union U @d',
		[
			"Directive @d isn't allowed to be placed on union type 'U'",
		],
	],
	'invalid union type extension with repeated non-repeatable directive' => [
		'union U' . RootQueryType(),
		'directive @d on UNION extend union U @d @d',
		[
			"Directive @d on union type 'U' can't be repeated",
		],
	],
	'invalid union type extension with already configured non-repeatable directive' => [
		'directive @d on UNION union U @d' . RootQueryType(),
		'extend union U @d',
		[
			"Union type 'U' can't be extended with already configured non-repeatable directive @d",
		],
	],
];
