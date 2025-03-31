<?php declare(strict_types=1);

if (function_exists('RootQueryType') === false) {
	function RootQueryType(): string { return '   ""   type Query'; }
}

return [
	'invalid empty document' => [
		'',
		[
			"Expected token 'extend', got end of stream",
		],
	],
	'invalid document without query root operation type' => [
		'enum A',
		[
			"Schema must define query root operation type 'Query'",
		],
	],
	'schema' => [
		'schema { query: Query } type Query',
		[],
	],
	'scalar' => [
		'scalar A' . RootQueryType(),
		[],
		[
			'A' => Vojtechdobes\GraphQL\Builtin\StringScalarImplementation::class,
		],
	],
	'enum' => [
		'enum A' . RootQueryType(),
		[],
	],
	'enum with single value' => [
		'enum A { A }' . RootQueryType(),
		[],
	],
	'enum with multiple values' => [
		'enum A { M N O }' . RootQueryType(),
		[],
	],
	'object with single non-nullable field' => [
		'type Query { x: String! }',
		[],
	],
	'object with single nullable field' => [
		'type Query { x: String }',
		[],
	],
	'object with single nullable list field with nullable item' => [
		'type Query { x: [String] }',
		[],
	],
	'object with single nullable list field with non-nullable item' => [
		'type Query { x: [String!] }',
		[],
	],
	'object with single non-nullable list field with nullable item' => [
		'type Query { x: [String]! }',
		[],
	],
	'object with single non-nullable list field with non-nullable item' => [
		'type Query { x: [String!]! }',
		[],
	],
	'object with multiple non-nullable fields' => [
		'type Query { x: String! bar: String! }',
		[],
	],
	'object field with single nullable string argument' => [
		'type Query { x(arg1: String): String }',
		[],
	],
	'object field with single nullable string argument with default value' => [
		'type Query { x(arg1: String = "foobar"): String }',
		[],
	],
	'object field with single nullable int argument with default value' => [
		'type Query { x(arg1: Int = 123): String }',
		[],
	],
	'object field with single nullable float (> 1) argument with default value' => [
		'type Query { x(arg1: Float = 123.5): String }',
		[],
	],
	'object field with single nullable float (< 0) argument with default value' => [
		'type Query { x(arg1: Float = 0.123): String }',
		[],
	],
	'object field with single nullable boolean argument with default value' => [
		'type Query { x(arg1: Boolean = true): String }',
		[],
	],
	'object field with single nullable list argument with default value' => [
		'type Query { x(arg1: [String!] = ["foobar"]): String }',
		[],
	],
	'object field with single nullable object argument with default value' => [
		'input A { x: String! } type Query { x(arg1: A = {x: "foobar"}): String }',
		[],
	],
	'union type with 2 members' => [
		'type A { a: String } type B { b: String} union C = A | B' . RootQueryType(),
		[],
	],
	'union type with 3 members' => [
		'type A { a: String } type B { b: String} union C = | A | B' . RootQueryType(),
		[],
	],
	// disallowed name prefix
	'invalid directive with 2-underscore name prefix' => [
		'directive @__d on ENUM',
		[
			"Directive name '__d' can't begin with '__', because such names are reserved for introspection",
		],
	],
	'invalid directive with argument with 2-underscore name prefix' => [
		'directive @d(__arg1: String) on ENUM',
		[
			"Argument of directive @d name '__arg1' can't begin with '__', because such names are reserved for introspection",
		],
	],
	'invalid enum type with 2-underscore name prefix' => [
		'enum __A',
		[
			"Enum type name '__A' can't begin with '__', because such names are reserved for introspection",
		],
	],
	'invalid enum type with value with 2-underscore name prefix' => [
		'enum A { __A }',
		[
			"Enum type 'A' value name '__A' can't begin with '__', because such names are reserved for introspection",
		],
	],
	'invalid input object type with 2-underscore name prefix' => [
		'input __A',
		[
			"Input object type name '__A' can't begin with '__', because such names are reserved for introspection",
		],
	],
	'invalid input object type with field with 2-underscore name prefix' => [
		'input A { __a: String }',
		[
			"Input object type 'A' field name '__a' can't begin with '__', because such names are reserved for introspection",
		],
	],
	'invalid interface type with 2-underscore name prefix' => [
		'interface __A',
		[
			"Interface type name '__A' can't begin with '__', because such names are reserved for introspection",
		],
	],
	'invalid interface type with field with 2-underscore name prefix' => [
		'interface A { __a: String }',
		[
			"Interface type 'A' field name '__a' can't begin with '__', because such names are reserved for introspection",
		],
	],
	'invalid interface type with field with argument with 2-underscore name prefix' => [
		'interface A { a(__arg1: String): String }',
		[
			"Argument of interface type field 'A.a' name '__arg1' can't begin with '__', because such names are reserved for introspection",
		],
	],
	'invalid object type with 2-underscore name prefix' => [
		'type __A',
		[
			"Object type name '__A' can't begin with '__', because such names are reserved for introspection",
		],
	],
	'invalid object type with field with 2-underscore name prefix' => [
		'type A { __a: String }',
		[
			"Object type 'A' field name '__a' can't begin with '__', because such names are reserved for introspection",
		],
	],
	'invalid object type with field with argument with 2-underscore name prefix' => [
		'type A { a(__arg1: String): String }',
		[
			"Argument of object type field 'A.a' name '__arg1' can't begin with '__', because such names are reserved for introspection",
		],
	],
	'invalid scalar type with 2-underscore name prefix' => [
		'scalar __A',
		[
			"Scalar type name '__A' can't begin with '__', because such names are reserved for introspection",
		],
		[
			'__A' => Vojtechdobes\GraphQL\Builtin\StringScalarImplementation::class,
		],
	],
	'invalid union type with 2-underscore name prefix' => [
		'union __A',
		[
			"Union type name '__A' can't begin with '__', because such names are reserved for introspection",
		],
	],
	// schema
	'invalid schema with unknown operation' => [
		'schema { x: Query }',
		[
			"Expected token 'subscription', got 'x' instead (line 1, col 10)",
		],
	],
	'invalid schema with duplicate operation type' => [
		'schema { query: A query: B } type A type B',
		[
			"Schema definition can't define root operation type 'query' multiple times",
		],
	],
	'invalid multiple schema' => [
		'schema { query: A } schema { query: B } type A type B',
		[
			"Schema definition can't be present multiple times",
		],
	],
	'invalid schema with unknown operation type' => [
		'schema { query: A }',
		[
			"Query root operation has unknown type 'A'",
		],
	],
	'invalid schema with non-object operation type' => [
		'schema { query: A } enum A',
		[
			"Query root operation must have object type, but enum type 'A' given",
		],
	],
	'invalid schema with enum operation type' => [
		'schema { query: A } enum A',
		[
			"Query root operation must have object type, but enum type 'A' given",
		],
	],
	'invalid schema with input operation type' => [
		'schema { query: A } input A',
		[
			"Query root operation must have object type, but input object type 'A' given",
		],
	],
	'invalid schema with interface operation type' => [
		'schema { query: A } interface A',
		[
			"Query root operation must have object type, but interface type 'A' given",
		],
	],
	'invalid schema with scalar operation type' => [
		'schema { query: A } scalar A',
		[
			"Query root operation must have object type, but scalar type 'A' given",
		],
		[
			'A' => Vojtechdobes\GraphQL\Builtin\StringScalarImplementation::class,
		],
	],
	'invalid schema with union operation type' => [
		'schema { query: A } union A',
		[
			"Query root operation must have object type, but union type 'A' given",
		],
	],
	'invalid type defined multiple times' => [
		'scalar A scalar A',
		[
			"Type 'A' can't be defined multiple times",
		],
		[
			'A' => Vojtechdobes\GraphQL\Builtin\StringScalarImplementation::class,
		],
	],
	// enum type
	'invalid enum type with single duplicate enum' => [
		'enum A { M M }',
		[
			"Enum type 'A' can't define value 'M' multiple times",
		],
	],
	// input object type
	'invalid input object type with single duplicate field' => [
		'input A { x: String x: String }',
		[
			"Input object type 'A' can't define field 'x' multiple times",
		],
	],
	'invalid input object type with single triplicate field' => [
		'input A { x: String x: String x: String }',
		[
			"Input object type 'A' can't define field 'x' multiple times",
		],
	],
	'invalid input object type with single field with unknown type' => [
		'input A { x: B }',
		[
			"Input object field 'A.x' has unknown type 'B'",
		],
	],
	'invalid input object type with multiple fields with unknown type' => [
		'input A { x: B y: C }',
		[
			"Input object field 'A.x' has unknown type 'B'",
			"Input object field 'A.y' has unknown type 'C'",
		],
	],
	'invalid input object type with single field with non-input type' => [
		'type A input B { x: A }',
		[
			"Input object field 'B.x' must have input type, but object type 'A' given",
		],
	],
	'invalid input object type with multiple fields with non-input type' => [
		'type A input B { x: A y: A }',
		[
			"Input object field 'B.x' must have input type, but object type 'A' given",
			"Input object field 'B.y' must have input type, but object type 'A' given",
		],
	],
	'invalid input object type with single field with interface type' => [
		'interface A input B { x: A }',
		[
			"Input object field 'B.x' must have input type, but interface type 'A' given",
		],
	],
	'invalid input object type with single field with object type' => [
		'type A input B { x: A }',
		[
			"Input object field 'B.x' must have input type, but object type 'A' given",
		],
	],
	'invalid input object type with single field with union type' => [
		'union A input B { x: A }',
		[
			"Input object field 'B.x' must have input type, but union type 'A' given",
		],
	],
	'invalid input object type with single direct non-nullable circular reference' => [
		'input A { a: A! }',
		[
			"Input object type 'A' can't reference itself in field 'a' by a chain of non-nullable fields",
		],
	],
	'invalid input object type with multiple direct non-nullable circular reference' => [
		'input A { a: A! b: A! }',
		[
			"Input object type 'A' can't reference itself in field 'a' by a chain of non-nullable fields",
			"Input object type 'A' can't reference itself in field 'b' by a chain of non-nullable fields",
		],
	],
	'invalid input object type with indirect non-nullable circular reference' => [
		'input A { b: B! } input B { a: A! }',
		[
			"Input object type 'A' can't reference itself in field 'b' by a chain of non-nullable fields",
			"Input object type 'B' can't reference itself in field 'a' by a chain of non-nullable fields",
		],
	],
	'valid input object type with direct nullable circular reference' => [
		'input A { a: A }' . RootQueryType(),
		[],
	],
	'valid input object type with indirect nullable circular reference' => [
		'input A { a: B } input B { a: A! }' . RootQueryType(),
		[],
	],
	'valid input object type with non-nullable circular reference in list' => [
		'input A { a: [A!]! }' . RootQueryType(),
		[],
	],
	'invalid input object type with single field with incompatible default value' => [
		'input A { a: String = true }' . RootQueryType(),
		[
			"Default value of input object field 'A.a' must be scalar 'String', but boolean value given",
		],
	],
	'valid input object type with single field with compatible default value' => [
		'input A { a: String = "A" }' . RootQueryType(),
		[],
	],
	// interface
	'invalid interface type that implements single unknown interface' => [
		'interface A implements B',
		[
			"Interface type 'A' can't implement unknown type 'B'",
		],
	],
	'invalid interface type that implements multiple unknown interfaces' => [
		'interface A implements B & C',
		[
			"Interface type 'A' can't implement unknown type 'B'",
			"Interface type 'A' can't implement unknown type 'C'",
		],
	],
	'invalid interface type that implements single non-interface type' => [
		'scalar A interface B implements A',
		[
			"Interface type 'B' can't implement scalar type 'A'",
		],
		[
			'A' => Vojtechdobes\GraphQL\Builtin\StringScalarImplementation::class,
		],
	],
	'invalid interface type that implements multiple non-interface types' => [
		'scalar A scalar B interface C implements A & B',
		[
			"Interface type 'C' can't implement scalar type 'A'",
			"Interface type 'C' can't implement scalar type 'B'",
		],
		[
			'A' => Vojtechdobes\GraphQL\Builtin\StringScalarImplementation::class,
			'B' => Vojtechdobes\GraphQL\Builtin\StringScalarImplementation::class,
		],
	],
	'invalid interface type that implements enum type' => [
		'enum A interface B implements A',
		[
			"Interface type 'B' can't implement enum type 'A'",
		],
	],
	'invalid interface type that implements input type' => [
		'input A interface B implements A',
		[
			"Interface type 'B' can't implement input object type 'A'",
		],
	],
	'invalid interface type that implements object type' => [
		'type A interface B implements A',
		[
			"Interface type 'B' can't implement object type 'A'",
		],
	],
	'invalid interface type that implements scalar type' => [
		'scalar A interface B implements A',
		[
			"Interface type 'B' can't implement scalar type 'A'",
		],
		[
			'A' => Vojtechdobes\GraphQL\Builtin\StringScalarImplementation::class,
		],
	],
	'invalid interface type that implements union type' => [
		'union A interface B implements A',
		[
			"Interface type 'B' can't implement union type 'A'",
		],
	],
	'invalid interface type that implements itself directly' => [
		'interface A implements A',
		[
			"Interface type 'A' can't implement itself directly or indirectly",
		],
	],
	'invalid interface type that implements itself indirectly' => [
		'interface A implements B interface B implements A',
		[
			"Interface type 'A' can't implement itself directly or indirectly",
			"Interface type 'B' can't implement itself directly or indirectly",
		],
	],
	'invalid interface type that does not implement transitive interface' => [
		'interface A implements B interface B implements C interface C',
		[
			"Interface type 'A' must directly implement interface 'C' because implemented interface 'B' implements it",
		],
	],
	'valid interface type that does implement transitive interface' => [
		'interface A implements B & C interface B implements C interface C' . RootQueryType(),
		[],
	],
	'invalid interface type with single duplicate field' => [
		'interface A { x: String x: String }',
		[
			"Interface type 'A' can't define field 'x' multiple times",
		],
	],
	'invalid interface type with single triplicate field' => [
		'interface A { x: String x: String x: String }',
		[
			"Interface type 'A' can't define field 'x' multiple times",
		],
	],
	'invalid interface type with single field with unknown type' => [
		'interface A { x: B }',
		[
			"Interface type field 'A.x' has unknown type 'B'",
		],
	],
	'invalid interface type with single field with input object type' => [
		'input IO interface A { x: IO }',
		[
			"Interface type field 'A.x' must have output type, but input object type 'IO' given",
		],
	],
	'invalid object type that implements single unknown interface' => [
		'type A implements B',
		[
			"Object type 'A' can't implement unknown type 'B'",
		],
	],
	'invalid object type that implements multiple unknown interfaces' => [
		'type A implements B & C',
		[
			"Object type 'A' can't implement unknown type 'B'",
			"Object type 'A' can't implement unknown type 'C'",
		],
	],
	'invalid object type that implements single non-interface type' => [
		'scalar A type B implements A',
		[
			"Object type 'B' can't implement scalar type 'A'",
		],
		[
			'A' => Vojtechdobes\GraphQL\Builtin\StringScalarImplementation::class,
		],
	],
	'invalid object type that implements multiple non-interface types' => [
		'scalar A scalar B type C implements A & B',
		[
			"Object type 'C' can't implement scalar type 'A'",
			"Object type 'C' can't implement scalar type 'B'",
		],
		[
			'A' => Vojtechdobes\GraphQL\Builtin\StringScalarImplementation::class,
			'B' => Vojtechdobes\GraphQL\Builtin\StringScalarImplementation::class,
		],
	],
	'invalid object type that implements enum type' => [
		'enum A type B implements A',
		[
			"Object type 'B' can't implement enum type 'A'",
		],
	],
	'invalid object type that implements input type' => [
		'input A type B implements A',
		[
			"Object type 'B' can't implement input object type 'A'",
		],
	],
	'invalid object type that implements object type' => [
		'type A type B implements A',
		[
			"Object type 'B' can't implement object type 'A'",
		],
	],
	'invalid object type that implements scalar type' => [
		'scalar A type B implements A',
		[
			"Object type 'B' can't implement scalar type 'A'",
		],
		[
			'A' => Vojtechdobes\GraphQL\Builtin\StringScalarImplementation::class,
		],
	],
	'invalid object type that implements union type' => [
		'union A type B implements A',
		[
			"Object type 'B' can't implement union type 'A'",
		],
	],
	'invalid object type that does not implement transitive interface' => [
		'type A implements B interface B implements C interface C',
		[
			"Object type 'A' must directly implement interface 'C' because implemented interface 'B' implements it",
		],
	],
	'valid object type that does implement transitive interface' => [
		'type A implements B & C interface B implements C interface C' . RootQueryType(),
		[],
	],
	'invalid object type with single duplicate field' => [
		'type A { x: String x: String }',
		[
			"Object type 'A' can't define field 'x' multiple times",
		],
	],
	'invalid object type with single triplicate field' => [
		'type A { x: String x: String x: String }',
		[
			"Object type 'A' can't define field 'x' multiple times",
		],
	],
	'invalid object type with single field with unknown type' => [
		'type A { x: B }',
		[
			"Object type field 'A.x' has unknown type 'B'",
		],
	],
	'invalid object type with multiple fields with unknown type' => [
		'type A { x: B y: C }',
		[
			"Object type field 'A.x' has unknown type 'B'",
			"Object type field 'A.y' has unknown type 'C'",
		],
	],
	'invalid object type with single field with non-output type' => [
		'input A type B { x: A }',
		[
			"Object type field 'B.x' must have output type, but input object type 'A' given",
		],
	],
	'invalid object type with multiple fields with non-output type' => [
		'input A type B { x: A, y: A }',
		[
			"Object type field 'B.x' must have output type, but input object type 'A' given",
			"Object type field 'B.y' must have output type, but input object type 'A' given",
		],
	],
	'invalid object type that implements but lacks single field' => [
		'interface A { a: String } type B implements A',
		[
			"Object type 'B' implements interface 'A' but doesn't have field 'a'",
		],
	],
	'invalid object type that implements but lacks multiple fields' => [
		'interface A { a: String b: String } type B implements A',
		[
			"Object type 'B' implements interface 'A' but doesn't have field 'a'",
			"Object type 'B' implements interface 'A' but doesn't have field 'b'",
		],
	],
	'invalid object type that implements but has single field with incorrect scalar type' => [
		'interface A { a: String } type B implements A { a: Int }',
		[
			"Object type field 'B.a' of type Int isn't covariant with interface type field 'A.a' of type String",
		],
	],
	'invalid object type that implements but has multiple fields with incorrect scalar type' => [
		'interface A { a: String b: String } type B implements A { a: Int b: Float }',
		[
			"Object type field 'B.a' of type Int isn't covariant with interface type field 'A.a' of type String",
			"Object type field 'B.b' of type Float isn't covariant with interface type field 'A.b' of type String",
		],
	],
	'invalid union with duplicate members' => [
		'type A union U = A | A',
		[
			"Union type 'U' can't include type 'A' multiple times",
		],
	],
	'invalid union with single unknown member' => [
		'union U = A',
		[
			"Union type 'U' can't include unknown type 'A'",
		],
	],
	'invalid union with multiple unknown members' => [
		'union U = A | B',
		[
			"Union type 'U' can't include unknown type 'A'",
			"Union type 'U' can't include unknown type 'B'",
		],
	],
	'invalid union with single non-object member' => [
		'scalar S union U = S',
		[
			"Union type 'U' can't include scalar type 'S'",
		],
		[
			'S' => Vojtechdobes\GraphQL\Builtin\StringScalarImplementation::class,
		],
	],
	'invalid union with multiple non-object members' => [
		'scalar S scalar T union U = S | T',
		[
			"Union type 'U' can't include scalar type 'S'",
			"Union type 'U' can't include scalar type 'T'",
		],
		[
			'S' => Vojtechdobes\GraphQL\Builtin\StringScalarImplementation::class,
			'T' => Vojtechdobes\GraphQL\Builtin\StringScalarImplementation::class,
		],
	],
	'invalid union with single enum member' => [
		'enum E union U = E',
		[
			"Union type 'U' can't include enum type 'E'",
		],
	],
	'invalid union with single input member' => [
		'input IO union U = IO',
		[
			"Union type 'U' can't include input object type 'IO'",
		],
	],
	'invalid union with single interface member' => [
		'interface I union U = I',
		[
			"Union type 'U' can't include interface type 'I'",
		],
	],
	'invalid union with single scalar member' => [
		'scalar S union U = S',
		[
			"Union type 'U' can't include scalar type 'S'",
		],
		[
			'S' => Vojtechdobes\GraphQL\Builtin\StringScalarImplementation::class,
		],
	],
	'invalid union with single union member' => [
		'union U union V = U',
		[
			"Union type 'V' can't include union type 'U'",
		],
	],
	'invalid union with self-referencing member' => [
		'union U = U',
		[
			"Union type 'U' can't include union type 'U'",
		],
	],
	'invalid directive with duplicate arguments' => [
		'directive @d(arg: String arg: String) on ENUM',
		[
			"Directive @d can't define argument 'arg' multiple times",
		],
	],
	'invalid interface type field with duplicate arguments' => [
		'interface A { x(arg: String arg: String): String }',
		[
			"Interface type field 'A.x' can't define argument 'arg' multiple times",
		],
	],
	'invalid object type field with duplicate arguments' => [
		'type A { x(arg: String arg: String): String }',
		[
			"Object type field 'A.x' can't define argument 'arg' multiple times",
		],
	],
	'invalid enum input with boolean default value' => [
		'enum A type B { x(arg1: A = true): String }',
		[
			"Default value of argument 'arg1' of object type field 'B.x' must be enum 'A', but boolean value given",
		],
	],
	'invalid enum input with string default value' => [
		'enum A type B { x(arg1: A = "X"): String }',
		[
			"Default value of argument 'arg1' of object type field 'B.x' must be enum 'A', but string value given",
		],
	],
	'invalid enum input with list default value' => [
		'enum A type B { x(arg1: A = ["X"]): String }',
		[
			"Default value of argument 'arg1' of object type field 'B.x' must be enum 'A', but list value given",
		],
	],
	'invalid enum input with unknown enum value' => [
		'enum A { M N O } type B { x(arg1: A = P): String }',
		[
			"Default value of argument 'arg1' of object type field 'B.x' with enum type 'A' doesn't recognize value 'P'",
		],
	],
	'invalid list input with non-list value' => [
		'type B { x(arg1: [String] = "X"): String }',
		[
			"Default value of argument 'arg1' of object type field 'B.x' must be [String], but string value given",
		],
	],
	'invalid object literal with single unknown field' => [
		'input A type B { x(arg1: A = {x: "X"}): String }',
		[
			"Default value of argument 'arg1' of object type field 'B.x' with input object type 'A' doesn't recognize field 'x'",
		],
	],
	'invalid object literal with multiple unknown fields' => [
		'input A type B { x(arg1: A = {x: "X", y: "Y"}): String }',
		[
			"Default value of argument 'arg1' of object type field 'B.x' with input object type 'A' doesn't recognize field 'x'",
			"Default value of argument 'arg1' of object type field 'B.x' with input object type 'A' doesn't recognize field 'y'",
		],
	],
	'invalid object literal with single duplicate field' => [
		'input A { x: String! } type B { x(arg1: A = {x: "X1", x: "X2"}): String }',
		[
			"Default value of argument 'arg1' of object type field 'B.x' with input object type can't define field 'x' multiple times",
		],
	],
	'invalid object literal with single triplicate field' => [
		'input A { x: String! } type B { x(arg1: A = {x: "X1", x: "X2", x: "X3"}): String }',
		[
			"Default value of argument 'arg1' of object type field 'B.x' with input object type can't define field 'x' multiple times",
		],
	],
	'invalid object literal with multiple duplicate fields' => [
		'input A { x: String! y: String! } type B { x(arg1: A = {x: "X1", x: "X2", y: "Y1", y: "Y2"}): String }',
		[
			"Default value of argument 'arg1' of object type field 'B.x' with input object type can't define field 'x' multiple times",
			"Default value of argument 'arg1' of object type field 'B.x' with input object type can't define field 'y' multiple times",
		],
	],
	'invalid object literal with single non-nullable string field with incorrect null value' => [
		'input A { x: String! } type B { x(arg1: A = {x: null}): String }',
		[
			"Value of field 'x' of default value of argument 'arg1' of object type field 'B.x' must be String!, but null value given",
		],
	],
	'invalid object literal with single non-nullable string field with missing value' => [
		'input A { x: String! } type B { x(arg1: A = {}): String }',
		[
			"Value of field 'x' of default value of argument 'arg1' of object type field 'B.x' must be String!, but null value given",
		],
	],
	'invalid object with missing colon after second field' => [
		'type Query { a: String! b String! }',
		[
			"Expected token ':', got 'String' instead (line 1, col 27)",
		],
	],
	'invalid non-nullable input with null default value' => [
		'type A { x(arg1: String! = null): String }',
		[
			"Default value of argument 'arg1' of object type field 'A.x' must be String!, but null value given",
		],
	],
	'invalid scalar string input with boolean default value' => [
		'type A { x(arg1: String = true): String }',
		[
			"Default value of argument 'arg1' of object type field 'A.x' must be scalar 'String', but boolean value given",
		],
	],
	'invalid scalar string input with enum default value' => [
		'type A { x(arg1: String = VALUE): String }',
		[
			"Default value of argument 'arg1' of object type field 'A.x' must be scalar 'String', but enum value given",
		],
	],
	'invalid scalar string input with int default value' => [
		'type A { x(arg1: String = 1): String }',
		[
			"Default value of argument 'arg1' of object type field 'A.x' must be scalar 'String', but integer value given",
		],
	],
	'invalid boolean input with string default value' => [
		'type A { x(arg1: Boolean = "A"): String }',
		[
			"Default value of argument 'arg1' of object type field 'A.x' must be scalar 'Boolean', but string value given",
		],
	],
	'invalid float input with string default value' => [
		'type A { x(arg1: Float = "A"): String }',
		[
			"Default value of argument 'arg1' of object type field 'A.x' must be scalar 'Float', but string value given",
		],
	],
	'invalid integer input with string default value' => [
		'type A { x(arg1: Int = "A"): String }',
		[
			"Default value of argument 'arg1' of object type field 'A.x' must be scalar 'Int', but string value given",
		],
	],
	'invalid directive usage of unknown directive' => [
		'enum E @d' . RootQueryType(),
		[
			"Directive @d on enum type 'E' isn't defined",
		],
	],
	'valid directive definition' => [
		'directive @d on ENUM' . RootQueryType(),
		[],
	],
	'valid directive usage' => [
		'directive @d on ENUM enum E @d' . RootQueryType(),
		[],
	],
	'invalid duplicate directive definition' => [
		'directive @d on ENUM directive @d on ENUM' . RootQueryType(),
		[
			"Directive 'd' can't be defined multiple times",
		],
	],
	'invalid directive usage on incorrect location' => [
		'directive @d on SCALAR enum E @d',
		[
			"Directive @d isn't allowed to be placed on enum type 'E'",
		],
	],
	'valid repeatable directive definition' => [
		'directive @d repeatable on ENUM' . RootQueryType(),
		[],
	],
	'invalid directive definition with malformed repeatable keyword' => [
		'directive @d repetable on ENUM',
		[
			"Expected token 'on', got 'repetable' instead (line 1, col 14)",
		],
	],
	'valid repeatable directive usage' => [
		'directive @d repeatable on ENUM enum E @d @d' . RootQueryType(),
		[],
	],
	'invalid repeated usage of non-repeatable directive' => [
		'directive @d on ENUM enum E @d @d',
		[
			"Directive @d on enum type 'E' can't be repeated",
		],
	],
	'valid directive usage with single argument' => [
		'directive @d(arg1: String) on ENUM enum E @d(arg1: "A")' . RootQueryType(),
		[],
	],
	'valid directive usage with multiple arguments' => [
		'directive @d(arg1: String arg2: String) on ENUM enum E @d(arg1: "A" arg2: "B")' . RootQueryType(),
		[],
	],
	'invalid directive usage of string argument with boolean value' => [
		'directive @d(arg1: String) on ENUM enum E @d(arg1: true)',
		[
			"Argument 'arg1' of Directive @d on enum type 'E' must be scalar 'String', but boolean value given",
		],
	],
	'invalid directive usage of string argument with float value' => [
		'directive @d(arg1: String) on ENUM enum E @d(arg1: 1.0)',
		[
			"Argument 'arg1' of Directive @d on enum type 'E' must be scalar 'String', but float value given",
		],
	],
	'invalid directive usage of string argument with int value' => [
		'directive @d(arg1: String) on ENUM enum E @d(arg1: 1)',
		[
			"Argument 'arg1' of Directive @d on enum type 'E' must be scalar 'String', but integer value given",
		],
	],
	'invalid directive usage of 2nd string argument with boolean value' => [
		'directive @d(arg1: String arg2: String) on ENUM enum E @d(arg2: true)',
		[
			"Argument 'arg2' of Directive @d on enum type 'E' must be scalar 'String', but boolean value given",
		],
	],
	'invalid 2nd directive usage of string argument with boolean value' => [
		'directive @d(arg1: String) repeatable on ENUM enum E @d @d(arg1: true)',
		[
			"Argument 'arg1' of Directive @d #2 on enum type 'E' must be scalar 'String', but boolean value given",
		],
	],
	'allowed directive on directive argument' => [
		'directive @d on ARGUMENT_DEFINITION directive @e(arg1: String @d arg2: String @d) on ENUM' . RootQueryType(),
		[],
	],
	'disallowed directive on directive argument' => [
		'directive @d on QUERY directive @e(arg1: String @d arg2: String @d) on ENUM',
		[
			"Directive @d isn't allowed to be placed on argument 'arg1' of directive @e",
			"Directive @d isn't allowed to be placed on argument 'arg2' of directive @e",
		],
	],
	'allowed directive on interface type field argument' => [
		'directive @d on ARGUMENT_DEFINITION interface A { a(arg1: String @d arg2: String @d): String }' . RootQueryType(),
		[],
	],
	'disallowed directive on interface type field argument' => [
		'directive @d on QUERY interface A { a(arg1: String @d arg2: String @d): String }',
		[
			"Directive @d isn't allowed to be placed on argument 'arg1' of interface type field 'A.a'",
			"Directive @d isn't allowed to be placed on argument 'arg2' of interface type field 'A.a'",
		],
	],
	'allowed directive on object type field argument' => [
		'directive @d on ARGUMENT_DEFINITION type A { a(arg1: String @d arg2: String  @d): String }' . RootQueryType(),
		[],
	],
	'disallowed directive on object type field argument' => [
		'directive @d on QUERY type A { a(arg1: String @d arg2: String  @d): String }',
		[
			"Directive @d isn't allowed to be placed on argument 'arg1' of object type field 'A.a'",
			"Directive @d isn't allowed to be placed on argument 'arg2' of object type field 'A.a'",
		],
	],
	'allowed directive on enum type' => [
		'directive @d on ENUM enum A @d' . RootQueryType(),
		[],
	],
	'disallowed directive on enum type' => [
		'directive @d on QUERY enum A @d',
		[
			"Directive @d isn't allowed to be placed on enum type 'A'",
		],
	],
	'allowed directive on enum value' => [
		'directive @d on ENUM_VALUE enum A { M @d N @d }' . RootQueryType(),
		[],
	],
	'disallowed directive on enum value' => [
		'directive @d on QUERY enum A { M @d N @d }',
		[
			"Directive @d isn't allowed to be placed on enum type 'A' value 'M'",
			"Directive @d isn't allowed to be placed on enum type 'A' value 'N'",
		],
	],
	'allowed directive on interface type field' => [
		'directive @d on FIELD_DEFINITION interface A { a: String @d b: Int @d }' . RootQueryType(),
		[],
	],
	'disallowed directive on interface type field' => [
		'directive @d on QUERY interface A { a: String @d b: Int @d }',
		[
			"Directive @d isn't allowed to be placed on interface type field 'A.a'",
			"Directive @d isn't allowed to be placed on interface type field 'A.b'",
		],
	],
	'allowed directive on object type field' => [
		'directive @d on FIELD_DEFINITION type A { a: String @d b: Int @d }' . RootQueryType(),
		[],
	],
	'disallowed directive on object type field' => [
		'directive @d on QUERY type A { a: String @d b: Int @d }',
		[
			"Directive @d isn't allowed to be placed on object type field 'A.a'",
			"Directive @d isn't allowed to be placed on object type field 'A.b'",
		],
	],
	'allowed directive on input object type field' => [
		'directive @d on INPUT_FIELD_DEFINITION input A { a: String @d }' . RootQueryType(),
		[],
	],
	'disallowed directive on input object type field' => [
		'directive @d on QUERY input A { a: String @d }',
		[
			"Directive @d isn't allowed to be placed on input object field 'A.a'",
		],
	],
	'allowed directive on input object type' => [
		'directive @d on INPUT_OBJECT input A @d' . RootQueryType(),
		[],
	],
	'disallowed directive on input object type' => [
		'directive @d on QUERY input A @d',
		[
			"Directive @d isn't allowed to be placed on input object type 'A'",
		],
	],
	'allowed directive on interface type' => [
		'directive @d on INTERFACE interface A @d' . RootQueryType(),
		[],
	],
	'disallowed directive on interface type' => [
		'directive @d on QUERY interface A @d',
		[
			"Directive @d isn't allowed to be placed on interface type 'A'",
		],
	],
	'allowed directive on object type' => [
		'directive @d on OBJECT type A @d' . RootQueryType(),
		[],
	],
	'disallowed directive on object type' => [
		'directive @d on QUERY type A @d',
		[
			"Directive @d isn't allowed to be placed on object type 'A'",
		],
	],
	'allowed directive on scalar type' => [
		'directive @d on SCALAR scalar A @d' . RootQueryType(),
		[],
		[
			'A' => Vojtechdobes\GraphQL\Builtin\StringScalarImplementation::class,
		],
	],
	'disallowed directive on scalar type' => [
		'directive @d on QUERY scalar A @d',
		[
			"Directive @d isn't allowed to be placed on scalar type 'A'",
		],
		[
			'A' => Vojtechdobes\GraphQL\Builtin\StringScalarImplementation::class,
		],
	],
	'allowed directive on schema' => [
		'directive @d on SCHEMA schema @d { query: Query } type Query',
		[],
	],
	'disallowed directive on schema' => [
		'directive @d on QUERY schema @d { query: Query } type Query',
		[
			"Directive @d isn't allowed to be placed on schema definition",
		],
	],
	'allowed directive on union type' => [
		'directive @d on UNION union A @d' . RootQueryType(),
		[],
	],
	'disallowed directive on union type' => [
		'directive @d on QUERY union A @d',
		[
			"Directive @d isn't allowed to be placed on union type 'A'",
		],
	],
	'invalid directive referencing itself directly' => [
		'directive @d(arg1: String @d) on ARGUMENT_DEFINITION',
		[
			"Directive @d can't reference itself directly or indirectly",
		],
	],
	'invalid directive referencing itself indirectly' => [
		'directive @d(arg1: String @e) on ARGUMENT_DEFINITION directive @e(arg1: String @d) on ARGUMENT_DEFINITION',
		[
			"Directive @d can't reference itself directly or indirectly",
			"Directive @e can't reference itself directly or indirectly",
		],
	],
];
