<?php declare(strict_types=1);

return [
	'same level merge' => [
		'type O { a: String b: String } type Query { a: O }',
		[
			'O.a' => ResolveFromParent(),
			'O.b' => ResolveFromParent(),
			'Query.a' => [
				'a' => 'Alice',
				'b' => 'Bob',
			],
		],
		'query { a { ... { a } ... { b } } }',
		[],
		[
			'data' => [
				'a' => [
					'a' => 'Alice',
					'b' => 'Bob',
				],
			],
		],
	],
	'level up merge' => [
		'type O { a: String b: String } type Query { a: O }',
		[
			'O.a' => ResolveFromParent(),
			'O.b' => ResolveFromParent(),
			'Query.a' => [
				'a' => 'Alice',
				'b' => 'Bob',
			],
		],
		'query { ... { a { a } } ... { a { b } } }',
		[],
		[
			'data' => [
				'a' => [
					'a' => 'Alice',
					'b' => 'Bob',
				],
			],
		],
	],
	'2 levels up merge' => [
		'type O { a: String b: String } type Query { a: O }',
		[
			'O.a' => ResolveFromParent(),
			'O.b' => ResolveFromParent(),
			'Query.a' => [
				'a' => 'Alice',
				'b' => 'Bob',
			],
		],
		'query { ... { a { ... { a } } } ... { a { ... { b } } } }',
		[],
		[
			'data' => [
				'a' => [
					'a' => 'Alice',
					'b' => 'Bob',
				],
			],
		],
	],
];
