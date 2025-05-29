<?php declare(strict_types=1);

return [
	'field with nullable root query type resolved in valid way' => [
		'type Query { a: String q: Query }',
		[
			'Query.a' => 'Alice',
		],
		'query { q { a } }',
		[],
		[
			'data' => [
				'q' => [
					'a' => 'Alice',
				],
			],
		],
	],
	'field with non-nullable root query type resolved in valid way' => [
		'type Query { a: String q: Query! }',
		[
			'Query.a' => 'Alice',
		],
		'query { q { a } }',
		[],
		[
			'data' => [
				'q' => [
					'a' => 'Alice',
				],
			],
		],
	],
	'field with nullable root query type resolved in valid way with failing nullable subfield' => [
		'type Query { a: String q: Query! }',
		[
			'Query.a' => FailResolvingWithMessage('Yikes'),
		],
		'query { q { a } }',
		[],
		[
			'data' => [
				'q' => [
					'a' => null,
				],
			],
			'errors' => [
				[
					'message' => 'Yikes',
					'path' => ['q', 'a'],
				],
			],
		],
	],
	'field with nullable root query type resolved in valid way with failing non-nullable subfield' => [
		'type Query { a: String! q: Query }',
		[
			'Query.a' => FailResolvingWithMessage('Yikes'),
		],
		'query { q { a } }',
		[],
		[
			'data' => [
				'q' => null,
			],
			'errors' => [
				[
					'message' => 'Yikes',
					'path' => ['q', 'a'],
				],
			],
		],
	],
	'field with non-nullable root query type resolved to null data because of failing non-nullable subfield' => [
		'type Query { a: String! q: Query! }',
		[
			'Query.a' => FailResolvingWithMessage('Yikes'),
		],
		'query { q { a } }',
		[],
		[
			'data' => null,
			'errors' => [
				[
					'message' => 'Yikes',
					'path' => ['q', 'a'],
				],
				[
					'message' => 'Non-nullable field resolved to null',
					'path' => ['q'],
				],
			],
		],
	],
];
