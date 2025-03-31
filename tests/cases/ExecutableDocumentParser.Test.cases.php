<?php declare(strict_types=1);

// '   ""   ' is used as a visual separator of mandatory 'type Query' which is included only to not cause invalid schema

return [
	'invalid empty document' => [
		'type Query',
		'',
		[
			"Expected token 'fragment', got end of stream",
		],
	],
	'alias' => [
		'type Query { a: String }',
		'query { x: a }',
		[],
	],
	'fragment' => [
		'type A { x: String y: String z: String} type Query { a: A }',
		'fragment F on A { x y z } query { a { ...F } }',
		[],
	],
	'unsupported mutation operation' => [
		'type Query',
		'mutation { a }',
		[
			"Schema doesn't support mutation operation",
		],
	],
	'unsupported subscription operation' => [
		'type Query',
		'subscription { a }',
		[
			"Schema doesn't support subscription operation",
		],
	],
	'default mutation operation type' => [
		'type Mutation { a: String }   ""   type Query',
		'mutation { a }',
		[],
	],
	'default query operation type' => [
		'type Query { a: String }',
		'query { a }',
		[],
	],
	'default subscription operation type' => [
		'type Subscription { a: String }   ""   type Query',
		'subscription { a }',
		[],
	],
	'short-hand query operation' => [
		'type Query { a: String }',
		'{ a }',
		[],
	],
	'valid anonymous operation in list of exactly 1 anonymous operation' => [
		'type Query { a: String }',
		'query { a }',
		[],
	],
	'invalid anonymous operation in list of 2 anonymous operations' => [
		'type Query { a: String }',
		'query { a } query { a }',
		[
			"In presence of multiple operations, anonymous operations aren't allowed",
		],
	],
	'invalid anonymous operation in list of 1 named and 1 anonymous operation' => [
		'type Query { a: String }',
		'query Q { a } query { a }',
		[
			"In presence of multiple operations, anonymous operations aren't allowed",
		],
	],
	'valid operation with single variable' => [
		'type Query { a: String }',
		'query Q($var1: String) { a }',
		[],
	],
	'invalid operation with single duplicate variable' => [
		'type Query { a: String }',
		'query Q($var1: String $var1: String) { a }',
		[
			"Query 'Q' can't define variable 'var1' multiple times",
		],
	],
	'invalid variable with unknown type' => [
		'type Query { a: String }',
		'query Q($var1: A) { a }',
		[
			"Variable 'var1' of Query 'Q' has unknown type 'A'",
		],
	],
	'invalid variable with interface type' => [
		'interface A type Query { a: String }',
		'query Q($var1: A) { a }',
		[
			"Variable 'var1' of Query 'Q' must have input type, but interface type 'A' given",
		],
	],
	'invalid variable with object type' => [
		'type A type Query { a: String }',
		'query Q($var1: A) { a }',
		[
			"Variable 'var1' of Query 'Q' must have input type, but object type 'A' given",
		],
	],
	'invalid variable with union type' => [
		'union A type Query { a: String }',
		'query Q($var1: A) { a }',
		[
			"Variable 'var1' of Query 'Q' must have input type, but union type 'A' given",
		],
	],
	'invalid string variable with bool default value' => [
		'type Query { a: String }',
		'query Q($var1: String = true) { a }',
		[
			"Default value of variable 'var1' of Query 'Q' must be scalar 'String', but boolean value given",
		],
	],
	'invalid string variable with float default value' => [
		'type Query { a: String }',
		'query Q($var1: String = 1.0) { a }',
		[
			"Default value of variable 'var1' of Query 'Q' must be scalar 'String', but float value given",
		],
	],
	'invalid string variable with int default value' => [
		'type Query { a: String }',
		'query Q($var1: String = 1) { a }',
		[
			"Default value of variable 'var1' of Query 'Q' must be scalar 'String', but integer value given",
		],
	],
	'invalid string variable with object default value' => [
		'type Query { a: String }',
		'query Q($var1: String = {}) { a }',
		[
			"Default value of variable 'var1' of Query 'Q' must be scalar 'String', but object value given",
		],
	],
	'invalid string variable with another string variable default value' => [
		'type Query { a: String }',
		'query Q($var1: String $var2: String = $var1) { a }',
		[
			"Variable 'var2' of Query 'Q' can't have another variable as it's default value",
		],
	],
	'invalid field with single unknown argument' => [
		'type Query { a: String }',
		'query { a(arg1: "A") }',
		[
			"Field 'a' can't accept unknown argument 'arg1'",
		],
	],
	'invalid field with multiple unknown arguments' => [
		'type Query { a: String }',
		'query { a(arg1: "A" arg2: "B") }',
		[
			"Field 'a' can't accept unknown argument 'arg1'",
			"Field 'a' can't accept unknown argument 'arg2'",
		],
	],
	'invalid field with duplicate argument' => [
		'type Query { a(arg1: String): String }',
		'query { a(arg1: "A" arg1: "B") }',
		[
			"Field 'a' can't accept argument 'arg1' multiple times",
		],
	],
	'invalid field argument with unknown variable' => [
		'type Query { a(arg1: String): String }',
		'query { a(arg1: $var1) }',
		[
			"Argument 'arg1' of Field 'a' references unknown variable 'var1'",
		],
	],
	'invalid non-nullable field argument with no value' => [
		'type Query { a(arg1: String!): String }',
		'query Q($var1: String) { a }',
		[
			"Argument 'arg1' of Field 'a' must be String!, but no value given",
		],
	],
	'invalid non-nullable field argument with null value' => [
		'type Query { a(arg1: String!): String }',
		'query { a(arg1: null ) }',
		[
			"Argument 'arg1' of Field 'a' must be String!, but null value given",
		],
	],
	'invalid non-nullable field argument with nullable variable' => [
		'type Query { a(arg1: String!): String }',
		'query Q($var1: String) { a(arg1: $var1) }',
		[
			"Argument 'arg1' of Field 'a' must be String!, but String variable 'var1' given",
		],
	],
	'invalid non-nullable field argument without default value with nullable variable without default value' => [
		'type Query { a(arg1: String!): String }',
		'query Q($var1: String) { a(arg1: $var1) }',
		[
			"Argument 'arg1' of Field 'a' must be String!, but String variable 'var1' given",
		],
	],
	'invalid non-nullable field argument with default null value with nullable variable without default value' => [
		'type Query { a(arg1: String!): String }',
		'query Q($var1: String = null) { a(arg1: $var1) }',
		[
			"Argument 'arg1' of Field 'a' must be String!, but String variable 'var1' given",
		],
	],
	'valid non-nullable field argument with default value with nullable variable without default value' => [
		'type Query { a(arg1: String! = "A"): String }',
		'query Q($var1: String) { a(arg1: $var1) }',
		[],
	],
	'valid non-nullable field argument without default value with nullable variable with non-nullable default value' => [
		'type Query { a(arg1: String!): String }',
		'query Q($var1: String = "A") { a(arg1: $var1) }',
		[],
	],
	'invalid input object field argument with boolean variable' => [
		'input I type Query { a(arg1: I): String }',
		'query Q($var1: Boolean) { a(arg1: $var1) }',
		[
			"Argument 'arg1' of Field 'a' must be I, but Boolean variable 'var1' given",
		],
	],
	'invalid input object field argument with boolean literal' => [
		'input I type Query { a(arg1: I): String }',
		'query { a(arg1: true) }',
		[
			"Argument 'arg1' of Field 'a' must be input object 'I', but boolean value given",
		],
	],
	'invalid input object field argument with float variable' => [
		'input I type Query { a(arg1: I): String }',
		'query Q($var1: Float) { a(arg1: $var1) }',
		[
			"Argument 'arg1' of Field 'a' must be I, but Float variable 'var1' given",
		],
	],
	'invalid input object field argument with float literal' => [
		'input I type Query { a(arg1: I): String }',
		'query { a(arg1: 123.456) }',
		[
			"Argument 'arg1' of Field 'a' must be input object 'I', but float value given",
		],
	],
	'invalid string field argument with boolean variable' => [
		'type Query { a(arg1: String): String }',
		'query Q($var1: Boolean) { a(arg1: $var1) }',
		[
			"Argument 'arg1' of Field 'a' must be String, but Boolean variable 'var1' given",
		],
	],
	'invalid string field argument with boolean literal' => [
		'type Query { a(arg1: String): String }',
		'query { a(arg1: true) }',
		[
			"Argument 'arg1' of Field 'a' must be scalar 'String', but boolean value given",
		],
	],
	'invalid selection of unknown field on query type' => [
		'type Query',
		'query { a }',
		[
			"Selection set on type 'Query' references unknown field 'a'",
		],
	],
	'invalid selection of unknown field on interface type' => [
		'interface A type Query { a: A }',
		'query { a { b } }',
		[
			"Selection set on type 'A' references unknown field 'b'",
		],
	],
	'invalid selection of unknown field on object type' => [
		'type A type Query { a: A }',
		'query { a { b } }',
		[
			"Selection set on type 'A' references unknown field 'b'",
		],
	],
	'invalid selection of interface type field without subselection' => [
		'interface A type Query { a: A }',
		'query { a }',
		[
			"Object field 'Query.a' has interface type 'A' which requires selecting subfields",
		],
	],
	'invalid selection of object type field without subselection' => [
		'type A type Query { a: A }',
		'query { a }',
		[
			"Object field 'Query.a' has object type 'A' which requires selecting subfields",
		],
	],
	'invalid selection of union type field without subselection' => [
		'union A type Query { a: A }',
		'query { a }',
		[
			"Object field 'Query.a' has union type 'A' which requires selecting subfields",
		],
	],
	'invalid selection of enum field with subselection' => [
		'enum A type Query { a: A }',
		'query { a { b } }',
		[
			"Object field 'Query.a' has enum type 'A' which doesn't support selecting subfields",
		],
	],
	'invalid selection of scalar field with subselection' => [
		'scalar A type Query { a: A }',
		'query { a { b } }',
		[
			"Object field 'Query.a' has scalar type 'A' which doesn't support selecting subfields",
		],
		[
			'A' => Vojtechdobes\GraphQL\Builtin\StringScalarImplementation::class,
		],
	],
	'invalid selection of object type field without subselection on interface type' => [
		'type A interface B { a: A } type Query { a: B }',
		'query { a { a } }',
		[
			"Object field 'B.a' has object type 'A' which requires selecting subfields",
		],
	],
	'invalid selection of enum type field with subselection on interface type' => [
		'enum A interface B { a: A } type Query { a: B }',
		'query { a { a { b } } }',
		[
			"Object field 'B.a' has enum type 'A' which doesn't support selecting subfields",
		],
	],
	'invalid selection using spread of unknown fragment' => [
		'type Query',
		'query { ... F }',
		[
			"Fragment 'F' is unknown",
		],
	],
	'invalid unused fragment' => [
		'type Query { a: String }',
		'fragment F on Query { a } query { a }',
		[
			"Fragment 'F' isn't used anywhere",
		],
	],
	'invalid duplicate fragment' => [
		'type Query { a: String }',
		'fragment F on Query { a } fragment F on Query { a }',
		[
			"Fragment 'F' can't be defined multiple times",
			"Document doesn't contain any executable operations",
		],
	],
	'inline fragment' => [
		'type A { x: String y: String z: String} type Query { a: A }',
		'query { a { ... on A { x y z } } }',
		[],
	],
	'invalid fragment with condition on unknown type' => [
		'type Query',
		'fragment F on A { a }',
		[
			"Type condition of fragment 'F' references unknown type 'A'",
			"Document doesn't contain any executable operations",
		],
	],
	'invalid fragment with condition on not allowed type' => [
		'enum A   ""   type Query',
		'fragment F on A { a }',
		[
			"Type condition of fragment 'F' must be interface, object or union, but enum type 'A' given",
			"Document doesn't contain any executable operations",
		],
	],
	'invalid fragment with condition on enum type' => [
		'enum A   ""   type Query',
		'fragment F on A { a }',
		[
			"Type condition of fragment 'F' must be interface, object or union, but enum type 'A' given",
			"Document doesn't contain any executable operations",
		],
	],
	'invalid fragment with condition on input object type' => [
		'input A { a: String }   ""   type Query',
		'fragment F on A { a }',
		[
			"Type condition of fragment 'F' must be interface, object or union, but input object type 'A' given",
			"Document doesn't contain any executable operations",
		],
	],
	'invalid fragment with condition on scalar type' => [
		'scalar A   ""   type Query',
		'fragment F on A { a }',
		[
			"Type condition of fragment 'F' must be interface, object or union, but scalar type 'A' given",
			"Document doesn't contain any executable operations",
		],
		[
			'A' => Vojtechdobes\GraphQL\Builtin\StringScalarImplementation::class,
		],
	],
	'invalid fragment spread in interface with condition on non-implementing type' => [
		'interface A type B { a: String } type Query { a: A }',
		'fragment F on B { a } query { a { ... F } }',
		[
			"Type condition of fragment 'F' spread on interface type 'A' must be on a compatible type, but object type 'B' given",
		],
	],
	'valid fragment spread in interface with condition on same interface' => [
		'interface A { a: String } type Query { a: A }',
		'fragment F on A { a } query { a { ... F } }',
		[],
	],
	'valid fragment spread in interface with condition on other interface' => [
		'interface A { a: String } interface B implements A { a: String } type Query { a: A }',
		'fragment F on B { a } query { a { ... F } }',
		[],
	],
	'invalid fragment spread in union with condition on non-member type' => [
		'union A type B { a: String } type Query { a: A }',
		'fragment F on B { a } query { a { ... F } }',
		[
			"Type condition of fragment 'F' spread on union type 'A' must be on its member type, but object type 'B' given",
		],
	],
	'invalid inline fragment in interface with condition on non-implementing object type' => [
		'interface A type B type Query { a: A }',
		'query { a { ... on B { x } } }',
		[
			"Type condition of inline fragment on interface type 'A' must be on object type implementing it, but object type 'B' given",
		],
	],
	'invalid inline fragment in union with condition on unknown type' => [
		'union A type Query { a: A }',
		'query { a { ... on B { x } } }',
		[
			"Type condition of inline fragment references unknown type 'B'",
		],
	],
	'invalid inline fragment in union with condition on non-member type' => [
		'union A type B type Query { a: A }',
		'query { a { ... on B { x } } }',
		[
			"Type condition of inline fragment on union type 'A' must be on its member type, but object type 'B' given",
		],
	],
	'invalid field selection on union type' => [
		'type A { a: String } union B = A type Query { a: B }',
		'query { a { a } }',
		[
			"Only fragments and __typename are allowed in selection set on union type 'B'",
		],
	],
	'valid __typename field selection on union type' => [
		'type A { a: String } union B = A type Query { a: B }',
		'query { a { __typename } }',
		[],
	],
	'allowed directive on field' => [
		'directive @d on FIELD type Query { a: String }',
		'query { a @d }',
		[],
	],
	'disallowed directive on field' => [
		'directive @d on ENUM type Query { a: String }',
		'query { a @d }',
		[
			"Directive @d isn't allowed to be placed on field 'a'",
		],
	],
	'allowed directive on fragment definition' => [
		'directive @d on FRAGMENT_DEFINITION type Query { a: String }',
		'fragment F on Query @d { a } query { ... F }',
		[],
	],
	'disallowed directive on fragment definition' => [
		'directive @d on ENUM type Query { a: String }',
		'fragment F on Query @d { a } query { ... F }',
		[
			"Directive @d isn't allowed to be placed on fragment 'F'",
		],
	],
	'allowed directive on fragment spread' => [
		'directive @d on FRAGMENT_SPREAD type Query { a: String }',
		'fragment F on Query { a } query { ... F @d }',
		[],
	],
	'disallowed directive on fragment spread' => [
		'directive @d on ENUM type Query { a: String }',
		'fragment F on Query { a } query { ... F @d }',
		[
			"Directive @d isn't allowed to be placed on spread of fragment 'F'",
		],
	],
	'allowed directive on inline fragment' => [
		'directive @d on INLINE_FRAGMENT type Query { a: String }',
		'query { ... on Query @d { a } }',
		[],
	],
	'disallowed directive on inline fragment' => [
		'directive @d on ENUM type Query { a: String }',
		'query { ... on Query @d { a } }',
		[
			"Directive @d isn't allowed to be placed on inline fragment on query",
		],
	],
	'allowed directive on mutation operation' => [
		'directive @d on MUTATION schema { mutation: Mutation } type Mutation { a: String }   ""   type Query',
		'mutation M @d { a }',
		[],
	],
	'disallowed directive on mutation operation' => [
		'directive @d on ENUM schema { mutation: Mutation } type Mutation { a: String }   ""   type Query',
		'mutation M @d { a }',
		[
			"Directive @d isn't allowed to be placed on mutation 'M'",
		],
	],
	'allowed directive on query operation' => [
		'directive @d on QUERY type Query { a: String }',
		'query Q @d { a }',
		[],
	],
	'disallowed directive on query operation' => [
		'directive @d on ENUM type Query { a: String }',
		'query Q @d { a }',
		[
			"Directive @d isn't allowed to be placed on query 'Q'",
		],
	],
	'allowed directive on subscription operation' => [
		'directive @d on SUBSCRIPTION schema { subscription: Subscription } type Subscription { a: String }   ""   type Query',
		'subscription S @d { a }',
		[],
	],
	'disallowed directive on subscription operation' => [
		'directive @d on ENUM schema { subscription: Subscription } type Subscription { a: String }   ""   type Query',
		'subscription S @d { a }',
		[
			"Directive @d isn't allowed to be placed on subscription 'S'",
		],
	],
	// 'invalid selection set on fragment with duplicate response key between 2 fields' => [
	// 	file_get_contents(__DIR__ . '/schema.graphqls'),
	// 	'fragment F on Abcd { a a }',
	// 	[
	// 		"Fragment 'F' can't define response key 'a' multiple times",
	// 	],
	// ],
	// 'invalid selection set on fragment with duplicate response key between 1 field and 1 alias' => [
	// 	file_get_contents(__DIR__ . '/schema.graphqls'),
	// 	'fragment F on Abcd { a a: b }',
	// 	[
	// 		"Fragment 'F' can't define response key 'a' multiple times",
	// 	],
	// ],
	// 'invalid selection set on fragment with duplicate response key between 2 aliases' => [
	// 	file_get_contents(__DIR__ . '/schema.graphqls'),
	// 	'fragment F on Abcd { a: b a: c }',
	// 	[
	// 		"Fragment 'F' can't define response key 'a' multiple times",
	// 	],
	// ],
	// 'invalid selection set on fragment with duplicate response key between 3 fields' => [
	// 	file_get_contents(__DIR__ . '/schema.graphqls'),
	// 	'fragment F on Abcd { a a a }',
	// 	[
	// 		"Fragment 'F' can't define response key 'a' multiple times",
	// 	],
	// ],
	// 'invalid selection set on fragment with duplicate response key between 1 field and 2 aliases' => [
	// 	file_get_contents(__DIR__ . '/schema.graphqls'),
	// 	'fragment F on Abcd { a a: b a: c }',
	// 	[
	// 		"Fragment 'F' can't define response key 'a' multiple times",
	// 	],
	// ],
	// 'invalid selection set on fragment with duplicate response key between 2 fields and 1 alias' => [
	// 	file_get_contents(__DIR__ . '/schema.graphqls'),
	// 	'fragment F on Abcd { a a a: b }',
	// 	[
	// 		"Fragment 'F' can't define response key 'a' multiple times",
	// 	],
	// ],
	// 'invalid selection set on fragment with duplicate response key between 3 aliases' => [
	// 	file_get_contents(__DIR__ . '/schema.graphqls'),
	// 	'fragment F on Abcd { a: b a: c a: d }',
	// 	[
	// 		"Fragment 'F' can't define response key 'a' multiple times",
	// 	],
	// ],
	// 'invalid selection set on query with duplicate response key between 2 fields' => [
	// 	file_get_contents(__DIR__ . '/schema.graphqls'),
	// 	'query { a a }',
	// 	[
	// 		"Query can't define response key 'a' multiple times",
	// 	],
	// ],
	// 'invalid selection set on query with duplicate response key between 1 field and 1 alias' => [
	// 	file_get_contents(__DIR__ . '/schema.graphqls'),
	// 	'query { a a: b }',
	// 	[
	// 		"Query can't define response key 'a' multiple times",
	// 	],
	// ],
	// 'invalid selection set on query with duplicate response key between 2 aliases' => [
	// 	file_get_contents(__DIR__ . '/schema.graphqls'),
	// 	'query { a: b a: c }',
	// 	[
	// 		"Query can't define response key 'a' multiple times",
	// 	],
	// ],
	// 'invalid selection set on query with duplicate response key between 3 fields' => [
	// 	file_get_contents(__DIR__ . '/schema.graphqls'),
	// 	'query { a a a }',
	// 	[
	// 		"Query can't define response key 'a' multiple times",
	// 	],
	// ],
	// 'invalid selection set on query with duplicate response key between 1 field and 2 aliases' => [
	// 	file_get_contents(__DIR__ . '/schema.graphqls'),
	// 	'query { a a: b a: c }',
	// 	[
	// 		"Query can't define response key 'a' multiple times",
	// 	],
	// ],
	// 'invalid selection set on query with duplicate response key between 2 fields and 1 alias' => [
	// 	file_get_contents(__DIR__ . '/schema.graphqls'),
	// 	'query { a a a: b }',
	// 	[
	// 		"Query can't define response key 'a' multiple times",
	// 	],
	// ],
	// 'invalid selection set on query with duplicate response key between 3 aliases' => [
	// 	file_get_contents(__DIR__ . '/schema.graphqls'),
	// 	'query { a: b a: c a: d }',
	// 	[
	// 		"Query can't define response key 'a' multiple times",
	// 	],
	// ],
	'valid introspection of __schema' => [
		'type Query',
		'query { __schema { description } }',
		[],
	],
	'invalid introspection of __schema on non-query root type' => [
		'type Mutation type Query',
		'mutation { __schema { description } }',
		[
			"Selection set on type 'Mutation' references unknown field '__schema'",
		],
	],
	'invalid introspection of __schema on non-root type' => [
		'type A type Query { a: A }',
		'query { a { __schema { description } } }',
		[
			"Selection set on type 'A' references unknown field '__schema'",
		],
	],
];
