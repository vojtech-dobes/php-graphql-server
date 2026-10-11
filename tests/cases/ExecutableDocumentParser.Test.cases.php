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
	'invalid string list field argument with boolean item' => [
		'type Query { a(arg1: [String]): String }',
		'query { a(arg1: ["A", true]) }',
		[
			"List item #2 in argument 'arg1' of Field 'a' must be scalar 'String', but boolean value given",
		],
	],
	'invalid string list list field argument with boolean item' => [
		'type Query { a(arg1: [[String]]): String }',
		'query { a(arg1: [["A"], [true]]) }',
		[
			"List item #1 in list item #2 in argument 'arg1' of Field 'a' must be scalar 'String', but boolean value given",
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
		'interface A { a: String } interface B implements A { a: String } type C implements A & B { a: String } type Query { a: A }',
		'fragment F on B { a } query { a { ... F } }',
		[],
	],
	'invalid fragment spread in interface with condition on other interface without implementing object type' => [
		'interface A { a: String } interface B implements A { a: String } type Query { a: A }',
		'fragment F on B { a } query { a { ... F } }',
		[
			"Type condition of fragment 'F' spread on interface type 'A' must be on a compatible type, but interface type 'B' given",
		],
	],
	'invalid fragment spread in union with condition on non-member type' => [
		'union A type B { a: String } type Query { a: A }',
		'fragment F on B { a } query { a { ... F } }',
		[
			"Type condition of fragment 'F' spread on union type 'A' must be on a compatible type, but object type 'B' given",
		],
	],
	'valid fragment spread in interface with condition on other interface sharing implementing type' => [
		'interface A { a: String } interface I { a: String } type B implements A & I { a: String } type Query { a: A }',
		'fragment F on I { __typename } query { a { ...F } }',
		[],
	],
	'invalid fragment spread in interface with condition on other interface not sharing implementing type' => [
		'interface A { a: String } interface I { a: String } type B implements A { a: String } type C implements I { a: String } type Query { a: A }',
		'fragment F on I { __typename } query { a { ...F } }',
		[
			"Type condition of fragment 'F' spread on interface type 'A' must be on a compatible type, but interface type 'I' given",
		],
	],
	'valid fragment spread in interface with condition on union with implementing member' => [
		'interface A { a: String } type B implements A { a: String } union U = B type Query { a: A }',
		'fragment F on U { __typename } query { a { ...F } }',
		[],
	],
	'invalid fragment spread in interface with condition on union without implementing member' => [
		'interface A { a: String } type B implements A { a: String } type C { c: String } union U = C type Query { a: A }',
		'fragment F on U { __typename } query { a { ...F } }',
		[
			"Type condition of fragment 'F' spread on interface type 'A' must be on a compatible type, but union type 'U' given",
		],
	],
	'valid fragment spread in union with condition on interface implemented by member' => [
		'interface I { a: String } type B implements I { a: String } union A = B type Query { a: A }',
		'fragment F on I { __typename } query { a { ...F } }',
		[],
	],
	'invalid fragment spread in union with condition on interface not implemented by member' => [
		'interface I { a: String } type B { a: String } type C implements I { a: String } union A = B type Query { a: A }',
		'fragment F on I { __typename } query { a { ...F } }',
		[
			"Type condition of fragment 'F' spread on union type 'A' must be on a compatible type, but interface type 'I' given",
		],
	],
	'valid fragment spread in union with condition on union sharing member' => [
		'type B { b: String } union A = B union U = B type Query { a: A }',
		'fragment F on U { __typename } query { a { ...F } }',
		[],
	],
	'invalid fragment spread in union with condition on union not sharing member' => [
		'type B { b: String } type C { c: String } union A = B union U = C type Query { a: A }',
		'fragment F on U { __typename } query { a { ...F } }',
		[
			"Type condition of fragment 'F' spread on union type 'A' must be on a compatible type, but union type 'U' given",
		],
	],
	'valid fragment spread in object with condition on same object type' => [
		'type A { a: String } type Query { a: A }',
		'fragment F on A { __typename } query { a { ...F } }',
		[],
	],
	'invalid fragment spread in object with condition on other object type' => [
		'type A { a: String } type B { b: String } type Query { a: A }',
		'fragment F on B { __typename } query { a { ...F } }',
		[
			"Type condition of fragment 'F' spread on object type 'A' must be on a compatible type, but object type 'B' given",
		],
	],
	'valid fragment spread in object with condition on implemented interface' => [
		'interface I { a: String } type A implements I { a: String } type Query { a: A }',
		'fragment F on I { __typename } query { a { ...F } }',
		[],
	],
	'invalid fragment spread in object with condition on non-implemented interface' => [
		'interface I { a: String } type A { a: String } type B implements I { a: String } type Query { a: A }',
		'fragment F on I { __typename } query { a { ...F } }',
		[
			"Type condition of fragment 'F' spread on object type 'A' must be on a compatible type, but interface type 'I' given",
		],
	],
	'valid fragment spread in object with condition on union with it as member' => [
		'type A { a: String } union U = A type Query { a: A }',
		'fragment F on U { __typename } query { a { ...F } }',
		[],
	],
	'invalid fragment spread in object with condition on union without it as member' => [
		'type A { a: String } type B { b: String } union U = B type Query { a: A }',
		'fragment F on U { __typename } query { a { ...F } }',
		[
			"Type condition of fragment 'F' spread on object type 'A' must be on a compatible type, but union type 'U' given",
		],
	],
	'valid fragment on object spread in interface' => [
		'interface A { a: String } type B implements A { a: String b: String } type Query { a: A }',
		'fragment F on B { b } query { a { ...F } }',
		[],
	],
	'valid fragment on object spread in union' => [
		'type B { b: String } union A = B type Query { a: A }',
		'fragment F on B { b } query { a { ...F } }',
		[],
	],
	'invalid fragment on object spread in interface with unknown field' => [
		'interface A { a: String } type B implements A { a: String b: String } type Query { a: A }',
		'fragment F on B { c } query { a { ...F } }',
		[
			"Selection set on type 'B' references unknown field 'c'",
		],
	],
	'invalid fragment on object spread in union with unknown field' => [
		'type B { b: String } union A = B type Query { a: A }',
		'fragment F on B { c } query { a { ...F } }',
		[
			"Selection set on type 'B' references unknown field 'c'",
		],
	],
	'invalid fragment with condition on enum type spread in object type' => [
		'enum A { X } type Query { a: String }',
		'fragment F on A { a } query { ...F }',
		[
			"Type condition of fragment 'F' must be interface, object or union, but enum type 'A' given",
		],
	],
	'invalid fragment spreading itself' => [
		'type Query { a: String }',
		'query { ...F } fragment F on Query { a ...F }',
		[
			"Fragment 'F' can't spread itself (F -> F)",
		],
	],
	'invalid fragment spreading itself in nested selection set' => [
		'type A { a: A } type Query { a: A }',
		'query { a { ...F } } fragment F on A { ... { a { ...F } } }',
		[
			"Fragment 'F' can't spread itself (F -> F)",
		],
	],
	'invalid fragments spreading each other' => [
		'type Query { a: String b: String }',
		'query { ...F1 } fragment F1 on Query { a ...F2 } fragment F2 on Query { b ...F1 }',
		[
			"Fragment 'F1' can't spread itself (F1 -> F2 -> F1)",
		],
	],
	'invalid unused fragment spreading itself' => [
		'type Query { a: String }',
		'query { a } fragment F on Query { a ...F }',
		[
			"Fragment 'F' isn't used anywhere",
		],
	],
	'invalid fragment spread multiple times with unknown field' => [
		'type Query { a: String }',
		'query { ...F ...F } fragment F on Query { x }',
		[
			"Selection set on type 'Query' references unknown field 'x'",
		],
	],
	'valid fragments spread exponentially many times' => [
		'type Query { a: String }',
		'query { ...F0 } fragment F0 on Query { a ...F1 ...F1 } fragment F1 on Query { a ...F2 ...F2 } fragment F2 on Query { a ...F3 ...F3 } fragment F3 on Query { a ...F4 ...F4 } fragment F4 on Query { a ...F5 ...F5 } fragment F5 on Query { a ...F6 ...F6 } fragment F6 on Query { a ...F7 ...F7 } fragment F7 on Query { a ...F8 ...F8 } fragment F8 on Query { a ...F9 ...F9 } fragment F9 on Query { a ...F10 ...F10 } fragment F10 on Query { a ...F11 ...F11 } fragment F11 on Query { a ...F12 ...F12 } fragment F12 on Query { a ...F13 ...F13 } fragment F13 on Query { a ...F14 ...F14 } fragment F14 on Query { a ...F15 ...F15 } fragment F15 on Query { a ...F16 ...F16 } fragment F16 on Query { a ...F17 ...F17 } fragment F17 on Query { a ...F18 ...F18 } fragment F18 on Query { a ...F19 ...F19 } fragment F19 on Query { a ...F20 ...F20 } fragment F20 on Query { a ...F21 ...F21 } fragment F21 on Query { a ...F22 ...F22 } fragment F22 on Query { a ...F23 ...F23 } fragment F23 on Query { a ...F24 ...F24 } fragment F24 on Query { a ...F25 ...F25 } fragment F25 on Query { a }',
		[],
	],
	'invalid inline fragment in interface with condition on non-implementing object type' => [
		'interface A type B type Query { a: A }',
		'query { a { ... on B { x } } }',
		[
			"Type condition of inline fragment on interface type 'A' must be on a compatible type, but object type 'B' given",
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
			"Type condition of inline fragment on union type 'A' must be on a compatible type, but object type 'B' given",
		],
	],
	'valid inline fragment in interface with condition on implementing object type' => [
		'interface A { a: String } type B implements A { a: String } type Query { a: A }',
		'query { a { ... on B { __typename } } }',
		[],
	],
	'valid inline fragment in interface with condition on same interface' => [
		'interface A { a: String } type B implements A { a: String } type Query { a: A }',
		'query { a { ... on A { __typename } } }',
		[],
	],
	'valid inline fragment in interface with condition on other interface sharing implementing type' => [
		'interface A { a: String } interface I { a: String } type B implements A & I { a: String } type Query { a: A }',
		'query { a { ... on I { __typename } } }',
		[],
	],
	'invalid inline fragment in interface with condition on other interface not sharing implementing type' => [
		'interface A { a: String } interface I { a: String } type B implements A { a: String } type C implements I { a: String } type Query { a: A }',
		'query { a { ... on I { __typename } } }',
		[
			"Type condition of inline fragment on interface type 'A' must be on a compatible type, but interface type 'I' given",
		],
	],
	'valid inline fragment in interface with condition on union with implementing member' => [
		'interface A { a: String } type B implements A { a: String } union U = B type Query { a: A }',
		'query { a { ... on U { __typename } } }',
		[],
	],
	'invalid inline fragment in interface with condition on union without implementing member' => [
		'interface A { a: String } type B implements A { a: String } type C { c: String } union U = C type Query { a: A }',
		'query { a { ... on U { __typename } } }',
		[
			"Type condition of inline fragment on interface type 'A' must be on a compatible type, but union type 'U' given",
		],
	],
	'valid inline fragment in union with condition on member type' => [
		'type B { b: String } union A = B type Query { a: A }',
		'query { a { ... on B { __typename } } }',
		[],
	],
	'valid inline fragment in union with condition on interface implemented by member' => [
		'interface I { a: String } type B implements I { a: String } union A = B type Query { a: A }',
		'query { a { ... on I { __typename } } }',
		[],
	],
	'invalid inline fragment in union with condition on interface not implemented by member' => [
		'interface I { a: String } type B { a: String } type C implements I { a: String } union A = B type Query { a: A }',
		'query { a { ... on I { __typename } } }',
		[
			"Type condition of inline fragment on union type 'A' must be on a compatible type, but interface type 'I' given",
		],
	],
	'valid inline fragment in union with condition on union sharing member' => [
		'type B { b: String } union A = B union U = B type Query { a: A }',
		'query { a { ... on U { __typename } } }',
		[],
	],
	'invalid inline fragment in union with condition on union not sharing member' => [
		'type B { b: String } type C { c: String } union A = B union U = C type Query { a: A }',
		'query { a { ... on U { __typename } } }',
		[
			"Type condition of inline fragment on union type 'A' must be on a compatible type, but union type 'U' given",
		],
	],
	'valid inline fragment in object with condition on same object type' => [
		'type A { a: String } type Query { a: A }',
		'query { a { ... on A { __typename } } }',
		[],
	],
	'invalid inline fragment in object with condition on other object type' => [
		'type A { a: String } type B { b: String } type Query { a: A }',
		'query { a { ... on B { __typename } } }',
		[
			"Type condition of inline fragment on object type 'A' must be on a compatible type, but object type 'B' given",
		],
	],
	'valid inline fragment in object with condition on implemented interface' => [
		'interface I { a: String } type A implements I { a: String } type Query { a: A }',
		'query { a { ... on I { __typename } } }',
		[],
	],
	'invalid inline fragment in object with condition on non-implemented interface' => [
		'interface I { a: String } type A { a: String } type B implements I { a: String } type Query { a: A }',
		'query { a { ... on I { __typename } } }',
		[
			"Type condition of inline fragment on object type 'A' must be on a compatible type, but interface type 'I' given",
		],
	],
	'valid inline fragment in object with condition on union with it as member' => [
		'type A { a: String } union U = A type Query { a: A }',
		'query { a { ... on U { __typename } } }',
		[],
	],
	'invalid inline fragment in object with condition on union without it as member' => [
		'type A { a: String } type B { b: String } union U = B type Query { a: A }',
		'query { a { ... on U { __typename } } }',
		[
			"Type condition of inline fragment on object type 'A' must be on a compatible type, but union type 'U' given",
		],
	],
	'invalid inline fragment in interface with condition on scalar type' => [
		'interface A { a: String } type B implements A { a: String } type Query { a: A }',
		'query { a { ... on String { __typename } } }',
		[
			"Type condition of inline fragment must be interface, object or union, but scalar type 'String' given",
		],
	],
	'invalid inline fragment in union with condition on scalar type' => [
		'type B { b: String } union A = B type Query { a: A }',
		'query { a { ... on String { __typename } } }',
		[
			"Type condition of inline fragment must be interface, object or union, but scalar type 'String' given",
		],
	],
	'invalid inline fragment in object with condition on scalar type' => [
		'type A { a: String } type Query { a: A }',
		'query { a { ... on String { __typename } } }',
		[
			"Type condition of inline fragment must be interface, object or union, but scalar type 'String' given",
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
	'invalid __typename field selection with argument' => [
		'type Query { a: String }',
		'query { __typename(x: 1) }',
		[
			"Field '__typename' can't accept unknown argument 'x'",
		],
	],
	'invalid __typename field selection with subselection' => [
		'type Query { a: String }',
		'query { __typename { a } }',
		[
			"Object field 'Query.__typename' has scalar type 'String' which doesn't support selecting subfields",
		],
	],
	'invalid __typename field selection with unknown directive' => [
		'type Query { a: String }',
		'query { __typename @d }',
		[
			"Directive @d on field '__typename' isn't defined",
		],
	],
	'invalid __typename field selection with invalid directive argument' => [
		'type Query { a: String }',
		'query { __typename @include(if: "A") }',
		[
			"Argument 'if' of Directive @include on field '__typename' must be scalar 'Boolean', but string value given",
		],
	],
	'invalid __typename field selection on union type with argument' => [
		'type A { a: String } union B = A type Query { a: B }',
		'query { a { __typename(x: 1) } }',
		[
			"Field '__typename' can't accept unknown argument 'x'",
		],
	],
	'invalid __typename field selection on union type with subselection' => [
		'type A { a: String } union B = A type Query { a: B }',
		'query { a { __typename { a } } }',
		[
			"Object field 'B.__typename' has scalar type 'String' which doesn't support selecting subfields",
		],
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
	'disallowed directives on field' => [
		'directive @d on ENUM directive @e on ENUM type Query { a: String }',
		'query { a @d @e }',
		[
			"Directive @d isn't allowed to be placed on field 'a'",
			"Directive @e isn't allowed to be placed on field 'a'",
		],
	],
	'unknown directive on field' => [
		'type Query { a: String }',
		'query { a @d }',
		[
			"Directive @d on field 'a' isn't defined",
		],
	],
	'unknown directives on field' => [
		'type Query { a: String }',
		'query { a @d @e }',
		[
			"Directive @d on field 'a' isn't defined",
			"Directive @e on field 'a' isn't defined",
		],
	],
	'duplicate directives on field' => [
		'directive @d on FIELD type Query { a: String }',
		'query { a @d @d }',
		[
			"Directive @d on field 'a' can't be repeated",
		],
	],
	'invalid directives on field' => [
		'directive @d on ENUM directive @f on FIELD type Query { a: String }',
		'query { a @d @d @e @e @f @f }',
		[
			"Directive @d #1 isn't allowed to be placed on field 'a'",
			"Directive @d #2 isn't allowed to be placed on field 'a'",
			"Directive @e #1 on field 'a' isn't defined",
			"Directive @e #2 on field 'a' isn't defined",
			"Directive @f on field 'a' can't be repeated",
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
	'disallowed directives on fragment definition' => [
		'directive @d on ENUM directive @e on ENUM type Query { a: String }',
		'fragment F on Query @d @e { a } query { ... F }',
		[
			"Directive @d isn't allowed to be placed on fragment 'F'",
			"Directive @e isn't allowed to be placed on fragment 'F'",
		],
	],
	'unknown directive on fragment definition' => [
		'type Query { a: String }',
		'fragment F on Query @d { a } query { ... F }',
		[
			"Directive @d on fragment 'F' isn't defined",
		],
	],
	'unknown directives on fragment definition' => [
		'type Query { a: String }',
		'fragment F on Query @d @e { a } query { ... F }',
		[
			"Directive @d on fragment 'F' isn't defined",
			"Directive @e on fragment 'F' isn't defined",
		],
	],
	'duplicate directives on fragment definition' => [
		'directive @d on FRAGMENT_DEFINITION type Query { a: String }',
		'fragment F on Query @d @d { a } query { ... F }',
		[
			"Directive @d on fragment 'F' can't be repeated",
		],
	],
	'invalid directives on fragment definition' => [
		'directive @d on ENUM directive @f on FRAGMENT_DEFINITION type Query { a: String }',
		'fragment F on Query @d @d @e @e @f @f { a } query { ... F }',
		[
			"Directive @d #1 isn't allowed to be placed on fragment 'F'",
			"Directive @d #2 isn't allowed to be placed on fragment 'F'",
			"Directive @e #1 on fragment 'F' isn't defined",
			"Directive @e #2 on fragment 'F' isn't defined",
			"Directive @f on fragment 'F' can't be repeated",
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
	'disallowed directives on fragment spread' => [
		'directive @d on ENUM directive @e on ENUM type Query { a: String }',
		'fragment F on Query { a } query { ... F @d @e }',
		[
			"Directive @d isn't allowed to be placed on spread of fragment 'F'",
			"Directive @e isn't allowed to be placed on spread of fragment 'F'",
		],
	],
	'unknown directive on fragment spread' => [
		'type Query { a: String }',
		'fragment F on Query { a } query { ... F @d }',
		[
			"Directive @d on spread of fragment 'F' isn't defined",
		],
	],
	'unknown directives on fragment spread' => [
		'type Query { a: String }',
		'fragment F on Query { a } query { ... F @d @e }',
		[
			"Directive @d on spread of fragment 'F' isn't defined",
			"Directive @e on spread of fragment 'F' isn't defined",
		],
	],
	'duplicate directives on fragment spread' => [
		'directive @d on FRAGMENT_SPREAD type Query { a: String }',
		'fragment F on Query { a } query { ... F @d @d }',
		[
			"Directive @d on spread of fragment 'F' can't be repeated",
		],
	],
	'invalid directives on fragment spread' => [
		'directive @d on ENUM directive @f on FRAGMENT_SPREAD type Query { a: String }',
		'fragment F on Query { a } query { ... F @d @d @e @e @f @f }',
		[
			"Directive @d #1 isn't allowed to be placed on spread of fragment 'F'",
			"Directive @d #2 isn't allowed to be placed on spread of fragment 'F'",
			"Directive @e #1 on spread of fragment 'F' isn't defined",
			"Directive @e #2 on spread of fragment 'F' isn't defined",
			"Directive @f on spread of fragment 'F' can't be repeated",
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
	'disallowed directives on inline fragment' => [
		'directive @d on ENUM directive @e on ENUM type Query { a: String }',
		'query { ... on Query @d @e { a } }',
		[
			"Directive @d isn't allowed to be placed on inline fragment on query",
			"Directive @e isn't allowed to be placed on inline fragment on query",
		],
	],
	'unknown directive on inline fragment' => [
		'type Query { a: String }',
		'query { ... on Query @d { a } }',
		[
			"Directive @d on inline fragment on query isn't defined",
		],
	],
	'unknown directives on inline fragment' => [
		'type Query { a: String }',
		'query { ... on Query @d @e { a } }',
		[
			"Directive @d on inline fragment on query isn't defined",
			"Directive @e on inline fragment on query isn't defined",
		],
	],
	'duplicate directives on inline fragment' => [
		'directive @d on INLINE_FRAGMENT type Query { a: String }',
		'query { ... on Query @d @d { a } }',
		[
			"Directive @d on inline fragment on query can't be repeated",
		],
	],
	'invalid directives on inline fragment' => [
		'directive @d on ENUM directive @f on INLINE_FRAGMENT type Query { a: String }',
		'query { ... on Query @d @d @e @e @f @f { a } }',
		[
			"Directive @d #1 isn't allowed to be placed on inline fragment on query",
			"Directive @d #2 isn't allowed to be placed on inline fragment on query",
			"Directive @e #1 on inline fragment on query isn't defined",
			"Directive @e #2 on inline fragment on query isn't defined",
			"Directive @f on inline fragment on query can't be repeated",
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
	'disallowed directives on mutation operation' => [
		'directive @d on ENUM directive @e on ENUM schema { mutation: Mutation } type Mutation { a: String }   ""   type Query',
		'mutation M @d @e { a }',
		[
			"Directive @d isn't allowed to be placed on mutation 'M'",
			"Directive @e isn't allowed to be placed on mutation 'M'",
		],
	],
	'unknown directive on mutation operation' => [
		'schema { mutation: Mutation } type Mutation { a: String }   ""   type Query',
		'mutation M @d { a }',
		[
			"Directive @d on mutation 'M' isn't defined",
		],
	],
	'unknown directives on mutation operation' => [
		'schema { mutation: Mutation } type Mutation { a: String }   ""   type Query',
		'mutation M @d @e { a }',
		[
			"Directive @d on mutation 'M' isn't defined",
			"Directive @e on mutation 'M' isn't defined",
		],
	],
	'duplicate directives on mutation operation' => [
		'directive @d on MUTATION schema { mutation: Mutation } type Mutation { a: String }   ""   type Query',
		'mutation M @d @d { a }',
		[
			"Directive @d on mutation 'M' can't be repeated",
		],
	],
	'invalid directives on mutation operation' => [
		'directive @d on ENUM directive @f on MUTATION schema { mutation: Mutation } type Mutation { a: String }   ""   type Query',
		'mutation M @d @d @e @e @f @f { a }',
		[
			"Directive @d #1 isn't allowed to be placed on mutation 'M'",
			"Directive @d #2 isn't allowed to be placed on mutation 'M'",
			"Directive @e #1 on mutation 'M' isn't defined",
			"Directive @e #2 on mutation 'M' isn't defined",
			"Directive @f on mutation 'M' can't be repeated",
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
	'disallowed directives on query operation' => [
		'directive @d on ENUM directive @e on ENUM type Query { a: String }',
		'query Q @d @e { a }',
		[
			"Directive @d isn't allowed to be placed on query 'Q'",
			"Directive @e isn't allowed to be placed on query 'Q'",
		],
	],
	'unknown directive on query operation' => [
		'type Query { a: String }',
		'query Q @d { a }',
		[
			"Directive @d on query 'Q' isn't defined",
		],
	],
	'unknown directives on query operation' => [
		'type Query { a: String }',
		'query Q @d @e { a }',
		[
			"Directive @d on query 'Q' isn't defined",
			"Directive @e on query 'Q' isn't defined",
		],
	],
	'duplicate directives on query operation' => [
		'directive @d on QUERY type Query { a: String }',
		'query Q @d @d { a }',
		[
			"Directive @d on query 'Q' can't be repeated",
		],
	],
	'invalid directives on query operation' => [
		'directive @d on ENUM directive @f on QUERY type Query { a: String }',
		'query Q @d @d @e @e @f @f { a }',
		[
			"Directive @d #1 isn't allowed to be placed on query 'Q'",
			"Directive @d #2 isn't allowed to be placed on query 'Q'",
			"Directive @e #1 on query 'Q' isn't defined",
			"Directive @e #2 on query 'Q' isn't defined",
			"Directive @f on query 'Q' can't be repeated",
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
	'disallowed directives on subscription operation' => [
		'directive @d on ENUM directive @e on ENUM schema { subscription: Subscription } type Subscription { a: String }   ""   type Query',
		'subscription S @d @e { a }',
		[
			"Directive @d isn't allowed to be placed on subscription 'S'",
			"Directive @e isn't allowed to be placed on subscription 'S'",
		],
	],
	'unknown directive on subscription operation' => [
		'schema { subscription: Subscription } type Subscription { a: String }   ""   type Query',
		'subscription S @d { a }',
		[
			"Directive @d on subscription 'S' isn't defined",
		],
	],
	'unknown directives on subscription operation' => [
		'schema { subscription: Subscription } type Subscription { a: String }   ""   type Query',
		'subscription S @d @e { a }',
		[
			"Directive @d on subscription 'S' isn't defined",
			"Directive @e on subscription 'S' isn't defined",
		],
	],
	'duplicate directives on subscription operation' => [
		'directive @d on SUBSCRIPTION schema { subscription: Subscription } type Subscription { a: String }   ""   type Query',
		'subscription S @d @d { a }',
		[
			"Directive @d on subscription 'S' can't be repeated",
		],
	],
	'invalid directives on subscription operation' => [
		'directive @d on ENUM directive @f on SUBSCRIPTION schema { subscription: Subscription } type Subscription { a: String }   ""   type Query',
		'subscription S @d @d @e @e @f @f { a }',
		[
			"Directive @d #1 isn't allowed to be placed on subscription 'S'",
			"Directive @d #2 isn't allowed to be placed on subscription 'S'",
			"Directive @e #1 on subscription 'S' isn't defined",
			"Directive @e #2 on subscription 'S' isn't defined",
			"Directive @f on subscription 'S' can't be repeated",
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
