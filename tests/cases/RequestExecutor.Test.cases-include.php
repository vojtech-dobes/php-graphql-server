<?php declare(strict_types=1);

return [
	'include field with literal true' => [
		'type Query { a: String }',
		[
			'Query.a' => 'A',
		],
		'query { a @include(if: true) }',
		[],
		[
			'data' => [
				'a' => 'A',
			],
		],
	],
	'include field with literal false' => [
		'type Query { a: String }',
		[
			'Query.a' => ShouldNotResolve(),
		],
		'query { a @include(if: false) }',
		[],
		[
			'data' => (object) [],
		],
	],
	'include field with variable true' => [
		'type Query { a: String }',
		[
			'Query.a' => 'A',
		],
		'query Q($var1: Boolean!) { a @include(if: $var1) }',
		[
			'var1' => true,
		],
		[
			'data' => [
				'a' => 'A',
			],
		],
	],
	'include field with variable false' => [
		'type Query { a: String }',
		[
			'Query.a' => ShouldNotResolve(),
		],
		'query Q($var1: Boolean!) { a @include(if: $var1) }',
		[
			'var1' => false,
		],
		[
			'data' => (object) [],
		],
	],
	'skip field with literal true' => [
		'type Query { a: String }',
		[
			'Query.a' => ShouldNotResolve(),
		],
		'query { a @skip(if: true) }',
		[],
		[
			'data' => (object) [],
		],
	],
	'skip field with literal false' => [
		'type Query { a: String }',
		[
			'Query.a' => 'A',
		],
		'query { a @skip(if: false) }',
		[],
		[
			'data' => [
				'a' => 'A',
			],
		],
	],
	'skip field with variable true' => [
		'type Query { a: String }',
		[
			'Query.a' => ShouldNotResolve(),
		],
		'query Q($var1: Boolean!) { a @skip(if: $var1) }',
		[
			'var1' => true,
		],
		[
			'data' => (object) [],
		],
	],
	'skip field with variable false' => [
		'type Query { a: String }',
		[
			'Query.a' => 'A',
		],
		'query Q($var1: Boolean!) { a @skip(if: $var1) }',
		[
			'var1' => false,
		],
		[
			'data' => [
				'a' => 'A',
			],
		],
	],
	'include inline fragment with literal true' => [
		'type Query { a: String }',
		[
			'Query.a' => 'A',
		],
		'query { ... @include(if: true) { a } }',
		[],
		[
			'data' => [
				'a' => 'A',
			],
		],
	],
	'include inline fragment with literal false' => [
		'type Query { a: String }',
		[
			'Query.a' => ShouldNotResolve(),
		],
		'query { ... @include(if: false) { a } }',
		[],
		[
			'data' => (object) [],
		],
	],
	'include inline fragment with variable true' => [
		'type Query { a: String }',
		[
			'Query.a' => 'A',
		],
		'query Q($var1: Boolean!) { ... @include(if: $var1) { a } }',
		[
			'var1' => true,
		],
		[
			'data' => [
				'a' => 'A',
			],
		],
	],
	'include inline fragment with variable false' => [
		'type Query { a: String }',
		[
			'Query.a' => ShouldNotResolve(),
		],
		'query Q($var1: Boolean!) { ... @include(if: $var1) { a } }',
		[
			'var1' => false,
		],
		[
			'data' => (object) [],
		],
	],
	'skip inline fragment with literal true' => [
		'type Query { a: String }',
		[
			'Query.a' => ShouldNotResolve(),
		],
		'query { ... @skip(if: true) { a } }',
		[],
		[
			'data' => (object) [],
		],
	],
	'skip inline fragment with literal false' => [
		'type Query { a: String }',
		[
			'Query.a' => 'A',
		],
		'query { ... @skip(if: false) { a } }',
		[],
		[
			'data' => [
				'a' => 'A',
			],
		],
	],
	'skip inline fragment with variable true' => [
		'type Query { a: String }',
		[
			'Query.a' => ShouldNotResolve(),
		],
		'query Q($var1: Boolean!) { ... @skip(if: $var1) { a } }',
		[
			'var1' => true,
		],
		[
			'data' => (object) [],
		],
	],
	'skip inline fragment with variable false' => [
		'type Query { a: String }',
		[
			'Query.a' => 'A',
		],
		'query Q($var1: Boolean!) { ... @skip(if: $var1) { a } }',
		[
			'var1' => false,
		],
		[
			'data' => [
				'a' => 'A',
			],
		],
	],
	'include fragment spread with literal true' => [
		'type Query { a: String }',
		[
			'Query.a' => 'A',
		],
		'fragment F on Query { a } query { ... F @include(if: true) }',
		[],
		[
			'data' => [
				'a' => 'A',
			],
		],
	],
	'include fragment spread with literal false' => [
		'type Query { a: String }',
		[
			'Query.a' => ShouldNotResolve(),
		],
		'fragment F on Query { a } query { ... F @include(if: false) }',
		[],
		[
			'data' => (object) [],
		],
	],
	'include fragment spread with variable true' => [
		'type Query { a: String }',
		[
			'Query.a' => 'A',
		],
		'fragment F on Query { a } query Q($var1: Boolean!) { ... F @include(if: $var1) }',
		[
			'var1' => true,
		],
		[
			'data' => [
				'a' => 'A',
			],
		],
	],
	'include fragment spread with variable false' => [
		'type Query { a: String }',
		[
			'Query.a' => ShouldNotResolve(),
		],
		'fragment F on Query { a } query Q($var1: Boolean!) { ... F @include(if: $var1) }',
		[
			'var1' => false,
		],
		[
			'data' => (object) [],
		],
	],
	'skip fragment spread with literal true' => [
		'type Query { a: String }',
		[
			'Query.a' => ShouldNotResolve(),
		],
		'fragment F on Query { a } query { ... F @skip(if: true) }',
		[],
		[
			'data' => (object) [],
		],
	],
	'skip fragment spread with literal false' => [
		'type Query { a: String }',
		[
			'Query.a' => 'A',
		],
		'fragment F on Query { a } query { ... F @skip(if: false) }',
		[],
		[
			'data' => [
				'a' => 'A',
			],
		],
	],
	'skip fragment spread with variable true' => [
		'type Query { a: String }',
		[
			'Query.a' => ShouldNotResolve(),
		],
		'fragment F on Query { a } query Q($var1: Boolean!) { ... F @skip(if: $var1) }',
		[
			'var1' => true,
		],
		[
			'data' => (object) [],
		],
	],
	'skip fragment spread with variable false' => [
		'type Query { a: String }',
		[
			'Query.a' => 'A',
		],
		'fragment F on Query { a } query Q($var1: Boolean!) { ... F @skip(if: $var1) }',
		[
			'var1' => false,
		],
		[
			'data' => [
				'a' => 'A',
			],
		],
	],
];
