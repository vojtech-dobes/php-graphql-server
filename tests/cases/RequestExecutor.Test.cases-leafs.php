<?php declare(strict_types=1);

return [
	'resolve boolean field' => [
		'type Query { a: Boolean }',
		[
			'Query.a' => true,
		],
		'query { a }',
		[],
		[
			'data' => [
				'a' => true,
			],
		],
	],
	'resolve float field' => [
		'type Query { a: Float }',
		[
			'Query.a' => 123.456,
		],
		'query { a }',
		[],
		[
			'data' => [
				'a' => 123.456,
			],
		],
	],
	'resolve int field' => [
		'type Query { a: Int }',
		[
			'Query.a' => 123,
		],
		'query { a }',
		[],
		[
			'data' => [
				'a' => 123,
			],
		],
	],
	'resolve string field' => [
		'type Query { a: String }',
		[
			'Query.a' => 'Alice',
		],
		'query { a }',
		[],
		[
			'data' => [
				'a' => 'Alice',
			],
		],
	],
	'resolve boolean field from default argument value' => [
		'type Query { a(arg1: Boolean = true): Boolean }',
		[
			'Query.a' => ResolveFromArgument('arg1'),
		],
		'query { a }',
		[],
		[
			'data' => [
				'a' => true,
			],
		],
	],
	'resolve float field from default argument value' => [
		'type Query { a(arg1: Float = 123.456): Float }',
		[
			'Query.a' => ResolveFromArgument('arg1'),
		],
		'query { a }',
		[],
		[
			'data' => [
				'a' => 123.456,
			],
		],
	],
	'resolve int field from default argument value' => [
		'type Query { a(arg1: Int = 123): Int }',
		[
			'Query.a' => ResolveFromArgument('arg1'),
		],
		'query { a }',
		[],
		[
			'data' => [
				'a' => 123,
			],
		],
	],
	'resolve string field from default argument value' => [
		'type Query { a(arg1: String = "Alice"): String }',
		[
			'Query.a' => ResolveFromArgument('arg1'),
		],
		'query { a }',
		[],
		[
			'data' => [
				'a' => 'Alice',
			],
		],
	],
	'resolve list field from default argument value' => [
		'type Query { a(arg1: [String!] = ["Alice"]): [String!] }',
		[
			'Query.a' => ResolveFromArgument('arg1'),
		],
		'query { a }',
		[],
		[
			'data' => [
				'a' => ['Alice'],
			],
		],
	],
	'resolve null field from default argument value' => [
		'type Query { a(arg1: String = null): String }',
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
	'resolve enum field from default argument value' => [
		'enum E { M } type Query { a(arg1: E = M): E }',
		[
			'Query.a' => ResolveFromArgument('arg1'),
		],
		'query { a }',
		[],
		[
			'data' => [
				'a' => 'M',
			],
		],
	],
	'resolve field using default input object argument value' => [
		'input IO { a: String } type Query { a(arg1: IO = { a: "Alice" }): String }',
		[
			'Query.a' => static fn ($objectValue, $fieldSelection) => $fieldSelection->arguments['arg1']['a'],
		],
		'query { a }',
		[],
		[
			'data' => [
				'a' => 'Alice',
			],
		],
	],
	'accept valid boolean variable' => [
		'type Query { a(arg1: Boolean): String }',
		[
			'Query.a' => 'A',
		],
		'query Q($var1: Boolean) { a(arg1: $var1) }',
		[
			'var1' => true,
		],
		[
			'data' => [
				'a' => 'A',
			],
		],
	],
	'accept valid float variable' => [
		'type Query { a(arg1: Float): String }',
		[
			'Query.a' => 'A',
		],
		'query Q($var1: Float) { a(arg1: $var1) }',
		[
			'var1' => 123.456,
		],
		[
			'data' => [
				'a' => 'A',
			],
		],
	],
	'accept valid integer variable' => [
		'type Query { a(arg1: Int): String }',
		[
			'Query.a' => 'A',
		],
		'query Q($var1: Int) { a(arg1: $var1) }',
		[
			'var1' => 123,
		],
		[
			'data' => [
				'a' => 'A',
			],
		],
	],
	'accept valid string variable' => [
		'type Query { a(arg1: String): String }',
		[
			'Query.a' => 'A',
		],
		'query Q($var1: String) { a(arg1: $var1) }',
		[
			'var1' => 'Alice',
		],
		[
			'data' => [
				'a' => 'A',
			],
		],
	],
	'accept valid list variable' => [
		'type Query { a(arg1: [String]): String }',
		[
			'Query.a' => 'A',
		],
		'query Q($var1: [String]) { a(arg1: $var1) }',
		[
			'var1' => ['Alice'],
		],
		[
			'data' => [
				'a' => 'A',
			],
		],
	],
	'reject invalid boolean variable' => [
		'type Query { a(arg1: Boolean): String }',
		[
			'Query.a' => ShouldNotResolve(),
		],
		'query Q($var1: Boolean) { a(arg1: $var1) }',
		[
			'var1' => 'Alice',
		],
		[
			'errors' => [
				[
					'message' => "Value for variable 'var1' must conform to Boolean",
				],
			],
		],
	],
	'reject invalid float variable' => [
		'type Query { a(arg1: Float): String }',
		[
			'Query.a' => ShouldNotResolve(),
		],
		'query Q($var1: Float) { a(arg1: $var1) }',
		[
			'var1' => 'Alice',
		],
		[
			'errors' => [
				[
					'message' => "Value for variable 'var1' must conform to Float",
				],
			],
		],
	],
	'reject invalid integer variable' => [
		'type Query { a(arg1: Int): String }',
		[
			'Query.a' => ShouldNotResolve(),
		],
		'query Q($var1: Int) { a(arg1: $var1) }',
		[
			'var1' => 'Alice',
		],
		[
			'errors' => [
				[
					'message' => "Value for variable 'var1' must conform to Int",
				],
			],
		],
	],
	'reject invalid string variable' => [
		'type Query { a(arg1: String): String }',
		[
			'Query.a' => ShouldNotResolve(),
		],
		'query Q($var1: String) { a(arg1: $var1) }',
		[
			'var1' => true,
		],
		[
			'errors' => [
				[
					'message' => "Value for variable 'var1' must conform to String",
				],
			],
		],
	],
	'reject invalid list variable' => [
		'type Query { a(arg1: [String]): String }',
		[
			'Query.a' => ShouldNotResolve(),
		],
		'query Q($var1: [String]) { a(arg1: $var1) }',
		[
			'var1' => true,
		],
		[
			'errors' => [
				[
					'message' => "Value for variable 'var1' must conform to [String]",
				],
			],
		],
	],
	'reject invalid input object variable' => [
		'input IO type Query { a(arg1: IO): String }',
		[
			'Query.a' => ShouldNotResolve(),
		],
		'query Q($var1: IO) { a(arg1: $var1) }',
		[
			'var1' => 'Alice',
		],
		[
			'errors' => [
				[
					'message' => "Value for variable 'var1' must conform to IO",
				],
			],
		],
	],
	'reject invalid input object variable field' => [
		'input IO { a: String } type Query { a(arg1: IO): String }',
		[
			'Query.a' => ShouldNotResolve(),
		],
		'query Q($var1: IO) { a(arg1: $var1) }',
		[
			'var1' => [
				'a' => true,
			],
		],
		[
			'errors' => [
				[
					'message' => "Value for variable 'var1' must conform to IO",
				],
			],
		],
	],
	'accept valid input object variable field with default value' => [
		'input IO { a: String = "Alice" } type Query { a(arg1: IO): String }',
		[
			'Query.a' => static fn ($objectValue, $fieldSelection) => $fieldSelection->arguments['arg1']['a'],
		],
		'query Q($var1: IO) { a(arg1: $var1) }',
		[
			'var1' => [],
		],
		[
			'data' => [
				'a' => 'Alice',
			],
		],
	],
];
