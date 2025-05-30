<?php declare(strict_types=1);

return [
	'field with wildcard field resolver resolves' => [
		'type Query { a: String }',
		[
			'Query.*' => 'Dude',
		],
		'query { a }',
		[],
		[
			'data' => [
				'a' => 'Dude',
			],
		],
	],
	'fields with field resolver and wildcard field resolver resolve' => [
		'type Query { a: String b: String c: String }',
		[
			'Query.a' => 'Alice',
			'Query.*' => 'Dude',
		],
		'query { a b c }',
		[],
		[
			'data' => [
				'a' => 'Alice',
				'b' => 'Dude',
				'c' => 'Dude',
			],
		],
	],
];
