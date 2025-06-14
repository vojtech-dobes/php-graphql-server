<?php declare(strict_types=1);

enum MyEnumClass: string
{

}

enum NotBackedEnumClass
{

}

return [
	'valid setup with no fields' => [
		'type Query',
		[],
		[],
		[],
		[],
		[],
	],
	'valid setup with single field' => [
		'type Query { a: String }',
		[],
		[
			'Query.a',
		],
		[],
		[],
		[],
	],
	'valid setup with multiple fields' => [
		'type Query { a: String b: String c: String }',
		[],
		[
			'Query.a',
			'Query.b',
			'Query.c',
		],
		[],
		[],
		[],
	],
	'invalid setup with single missing field resolver' => [
		'type Query { a: String }',
		[],
		[],
		[],
		[],
		[
			"Field 'Query.a' doesn't have a resolver",
		],
	],
	'invalid setup with multiple missing field resolvers' => [
		'type Query { a: String b: String c: String }',
		[],
		[],
		[],
		[],
		[
			"Field 'Query.a' doesn't have a resolver",
			"Field 'Query.b' doesn't have a resolver",
			"Field 'Query.c' doesn't have a resolver",
		],
	],
	'invalid setup with single unknown field resolver' => [
		'type Query',
		[],
		[
			'Query.a',
		],
		[],
		[],
		[
			"Field 'Query.a' has resolver but doesn't exist in schema",
		],
	],
	'invalid setup with multiple unknown field resolvers' => [
		'type Query',
		[],
		[
			'Query.a',
			'Query.b',
			'X.y',
		],
		[],
		[],
		[
			"Field 'Query.a' has resolver but doesn't exist in schema",
			"Field 'Query.b' has resolver but doesn't exist in schema",
			"Field 'X.y' has resolver but doesn't exist in schema",
		],
	],
	'valid setup with field with root query type without resolver' => [
		'type Query { a: Query }',
		[],
		[],
		[],
		[],
		[],
	],
	'invalid setup with field with root query type with resolver' => [
		'type Query { a: Query }',
		[],
		[
			'Query.a',
		],
		[],
		[],
		[
			"Field 'Query.a' resolving to root type can't have a resolver",
		],
	],
	'valid setup with single field with wildcard field resolver' => [
		'type Query { a: String }',
		[],
		[
			'Query',
		],
		[],
		[],
		[],
	],
	'valid setup with multiple fields with wildcard field resolver' => [
		'type Query { a: String b: String c: String }',
		[],
		[
			'Query',
		],
		[],
		[],
		[],
	],
	'valid setup with mix of fields with field resolver and wildcard field resolver' => [
		'type Query { a: String b: String c: String }',
		[],
		[
			'Query.a',
			'Query',
		],
		[],
		[],
		[],
	],
	'invalid setup with unknown field resolver in presence of wildcard field resolver' => [
		'type Query { a: String }',
		[],
		[
			'Query.b',
			'Query',
		],
		[],
		[],
		[
			"Field 'Query.b' has resolver but doesn't exist in schema",
		],
	],
	'invalid setup with unused wildcard field resolver' => [
		'type Query { a: String }',
		[],
		[
			'Query.a',
			'Query',
		],
		[],
		[],
		[
			"Type 'Query' has unused wildcard field resolver",
		],
	],
	'valid setup with single interface type' => [
		'interface I type Query',
		[
			'I',
		],
		[],
		[],
		[],
		[],
	],
	'valid setup with multiple interface types' => [
		'interface I interface J interface K type Query',
		[
			'I',
			'J',
			'K',
		],
		[],
		[],
		[],
		[],
	],
	'invalid setup with single missing interface type resolver' => [
		'interface I type Query { a: I }',
		[],
		[
			'Query.a',
		],
		[],
		[],
		[
			"Abstract interface type 'I' doesn't have a resolver",
		],
	],
	'invalid setup with multiple missing interface type resolvers' => [
		'interface I interface J interface K type Query { a: I b: J c: K }',
		[],
		[
			'Query.a',
			'Query.b',
			'Query.c',
		],
		[],
		[],
		[
			"Abstract interface type 'I' doesn't have a resolver",
			"Abstract interface type 'J' doesn't have a resolver",
			"Abstract interface type 'K' doesn't have a resolver",
		],
	],
	'valid setup with single missing interface type resolver when interface type is not directly returned' => [
		'interface I type Query',
		[],
		[],
		[],
		[],
		[],
	],
	'valid setup with multiple missing interface type resolvers when interface types are not directly returned' => [
		'interface I interface J interface K type Query',
		[],
		[],
		[],
		[],
		[],
	],
	'valid setup with single union type' => [
		'union U type Query',
		[
			'U',
		],
		[],
		[],
		[],
		[],
	],
	'valid setup with multiple union types' => [
		'union U union V union W type Query',
		[
			'U',
			'V',
			'W',
		],
		[],
		[],
		[],
		[],
	],
	'invalid setup with single missing union type resolver' => [
		'union U type Query { a: U }',
		[],
		[
			'Query.a',
		],
		[],
		[],
		[
			"Abstract union type 'U' doesn't have a resolver",
		],
	],
	'invalid setup with multiple missing union type resolvers' => [
		'union U union V union W type Query { a: U b: V c: W }',
		[],
		[
			'Query.a',
			'Query.b',
			'Query.c',
		],
		[],
		[],
		[
			"Abstract union type 'U' doesn't have a resolver",
			"Abstract union type 'V' doesn't have a resolver",
			"Abstract union type 'W' doesn't have a resolver",
		],
	],
	'valid setup with single missing union type resolver when union type is not directly returned' => [
		'union U type Query',
		[],
		[],
		[],
		[],
		[],
	],
	'valid setup with multiple missing union type resolvers when union types are not directly returned' => [
		'union U union V union W type Query',
		[],
		[],
		[],
		[],
		[],
	],
	'invalid setup with single unknown abstract type resolver' => [
		'type Query',
		[
			'I',
		],
		[],
		[],
		[],
		[
			"Abstract type 'I' has resolver but doesn't exist in schema",
		],
	],
	'invalid setup with multiple unknown abstract type resolvers' => [
		'type Query',
		[
			'I',
			'U',
			'X',
		],
		[],
		[],
		[],
		[
			"Abstract type 'I' has resolver but doesn't exist in schema",
			"Abstract type 'U' has resolver but doesn't exist in schema",
			"Abstract type 'X' has resolver but doesn't exist in schema",
		],
	],
	'valid setup with malformed schema' => [
		'typ Quarry',
		[],
		[],
		[],
		[],
		[
			"Expected token 'extend', got 'typ' instead (line 1, col 1)",
		],
	],
	'invalid setup with implementation for unknown scalar type' => [
		'type Query',
		[],
		[],
		[],
		[
			'S' => Vojtechdobes\GraphQL\Builtin\StringScalarImplementation::class,
		],
		[
			"Scalar implementation can't be registered for unknown type 'S'",
		],
	],
	'invalid setup with implementation for non-scalar type' => [
		'type Query',
		[],
		[],
		[],
		[
			'Query' => Vojtechdobes\GraphQL\Builtin\StringScalarImplementation::class,
		],
		[
			"Scalar implementation can't be registered for object type 'Query'",
		],
	],
	'invalid setup with missing scalar implementation' => [
		'type Query scalar S',
		[],
		[],
		[],
		[],
		[
			"Scalar type 'S' doesn't have an implementation",
		],
	],
	'invalid setup with enum class for unknown enum type' => [
		'type Query',
		[],
		[],
		[
			'E' => MyEnumClass::class,
		],
		[],
		[
			"Enum class can't be registered for unknown type 'E'",
		],
	],
	'invalid setup with enum class for non-enum type' => [
		'type Query',
		[],
		[],
		[
			'Query' => MyEnumClass::class,
		],
		[],
		[
			"Enum class can't be registered for object type 'Query'",
		],
	],
	'invalid setup with not-backed enum class' => [
		'type Query enum E',
		[],
		[],
		[
			'E' => NotBackedEnumClass::class,
		],
		[],
		[
			"Enum class for enum type 'E' must be a BackedEnum",
		],
	],
];
