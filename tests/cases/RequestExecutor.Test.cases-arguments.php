<?php declare(strict_types=1);

return [
	'argument with nullable string type without default value provided with no value' => [
		'type Query { a(arg1: String): String }',
		[
			'Query.a' => ResolveFromArgument('arg1'),
		],
		'query { a }',
		[],
		[
			'data' => [
				'a' => null,
			],
		],
	],
	'argument with nullable string type with non-null default value provided with nullable variable without default value given no value' => [
		'type Query { a(arg1: String = "Alice"): String }',
		[
			'Query.a' => ResolveFromArgument('arg1'),
		],
		'query Q($var1: String) { a(arg1: $var1) }',
		[],
		[
			'data' => [
				'a' => 'Alice',
			],
		],
	],
	'argument with nullable string type with non-null default value provided with nullable variable with default value given no value' => [
		'type Query { a(arg1: String = "Alice"): String }',
		[
			'Query.a' => ResolveFromArgument('arg1'),
		],
		'query Q($var1: String = "Bob") { a(arg1: $var1) }',
		[],
		[
			'data' => [
				'a' => 'Bob',
			],
		],
	],
	'argument with nullable string type with non-null default value provided with nullable variable with default value given null value' => [
		'type Query { a(arg1: String = "Alice"): String }',
		[
			'Query.a' => ResolveFromArgument('arg1'),
		],
		'query Q($var1: String = "Bob") { a(arg1: $var1) }',
		[
			'var1' => null,
		],
		[
			'data' => [
				'a' => 'Bob',
			],
		],
	],
	'argument with nullable string type with non-null default value provided with non-nullable variable with default value given null value' => [
		'type Query { a(arg1: String = "Alice"): String }',
		[
			'Query.a' => ResolveFromArgument('arg1'),
		],
		'query Q($var1: String! = "Bob") { a(arg1: $var1) }',
		[
			'var1' => null,
		],
		[
			'errors' => [
				[
					'message' => "Value for variable 'var1' must conform to String!",
				],
			],
		],
	],
	'argument with nullable string type with non-null default value provided with non-nullable variable without default value given valid value' => [
		'type Query { a(arg1: String = "Alice"): String }',
		[
			'Query.a' => ResolveFromArgument('arg1'),
		],
		'query Q($var1: String!) { a(arg1: $var1) }',
		[
			'var1' => 'Bob',
		],
		[
			'data' => [
				'a' => 'Bob',
			],
		],
	],
	'argument with nullable string type with non-null default value provided with non-nullable variable with default value given valid value' => [
		'type Query { a(arg1: String = "Alice"): String }',
		[
			'Query.a' => ResolveFromArgument('arg1'),
		],
		'query Q($var1: String! = "Bob") { a(arg1: $var1) }',
		[
			'var1' => 'Charlie',
		],
		[
			'data' => [
				'a' => 'Charlie',
			],
		],
	],
];
