<?php declare(strict_types=1);

/**
 * @return callable(): never
 */
function FailResolvingWithMessage(string $message): callable {
	return static fn () => throw new Vojtechdobes\GraphQL\Exceptions\FailedToResolveFieldException($message);
}

/**
 * @return callable(mixed, Vojtechdobes\GraphQL\FieldSelection): mixed
 */
function ResolveFromArgument(string $argumentName): callable {
	return static fn ($parent, $field) => $field->arguments[$argumentName];
}

/**
 * @return callable(mixed, Vojtechdobes\GraphQL\FieldSelection): mixed
 */
function ResolveFromParent(): callable {
	return static fn ($parent, $field) => $parent[$field->name];
}

/**
 * @return callable(): mixed
 */
function ShouldNotResolve(): callable {
	return static fn () => Tester\Assert::fail("shouldn't be called");
}

/**
 * @return callable(mixed): string
 */
function ResolveAbstractTypeByField(string $fieldName): callable {
	return static fn ($objectValue) => $objectValue[$fieldName];
}

return [
	...require_once __DIR__ . '/RequestExecutor.Test.cases-arguments.php',
	...require_once __DIR__ . '/RequestExecutor.Test.cases-deferred.php',
	...require_once __DIR__ . '/RequestExecutor.Test.cases-include.php',
	...require_once __DIR__ . '/RequestExecutor.Test.cases-introspection.php',
	...require_once __DIR__ . '/RequestExecutor.Test.cases-leafs.php',
	...require_once __DIR__ . '/RequestExecutor.Test.cases-merging.php',
	...require_once __DIR__ . '/RequestExecutor.Test.cases-rootTypeField.php',
	...require_once __DIR__ . '/RequestExecutor.Test.cases-wildcardFieldResolver.php',
	'valid operation with single direct field' => [
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
	'valid operation with multiple direct fields' => [
		'type Query { a: String b: String }',
		[
			'Query.a' => 'Alice',
			'Query.b' => 'Bob',
		],
		'query { a b }',
		[],
		[
			'data' => [
				'a' => 'Alice',
				'b' => 'Bob',
			],
		],
	],
	'valid operation with single direct field and single field via inline fragment without type condition' => [
		'type Query { a: String b: String }',
		[
			'Query.a' => 'Alice',
			'Query.b' => 'Bob',
		],
		'query { a ... { b } }',
		[],
		[
			'data' => [
				'a' => 'Alice',
				'b' => 'Bob',
			],
		],
	],
	'valid operation with single direct field and single field via inline fragment with type condition' => [
		'type Query { a: String b: String }',
		[
			'Query.a' => 'Alice',
			'Query.b' => 'Bob',
		],
		'query { a ... on Query { b } }',
		[],
		[
			'data' => [
				'a' => 'Alice',
				'b' => 'Bob',
			],
		],
	],
	'valid operation with single direct field and single field via fragment spread' => [
		'type Query { a: String b: String }',
		[
			'Query.a' => 'Alice',
			'Query.b' => 'Bob',
		],
		'fragment F on Query { b } query { a ...F }',
		[],
		[
			'data' => [
				'a' => 'Alice',
				'b' => 'Bob',
			],
		],
	],
	'valid operation with duplicate direct selection of a field' => [
		'type Query { a: String b: String }',
		[
			'Query.a' => 'Alice',
			'Query.b' => 'Bob',
		],
		'query { a b b }',
		[],
		[
			'data' => [
				'a' => 'Alice',
				'b' => 'Bob',
			],
		],
	],
	'valid operation with duplicate direct selection of a field and selection via inline fragment' => [
		'type Query { a: String b: String }',
		[
			'Query.a' => 'Alice',
			'Query.b' => 'Bob',
		],
		'query { a b ... { b } }',
		[],
		[
			'data' => [
				'a' => 'Alice',
				'b' => 'Bob',
			],
		],
	],
	'valid operation with duplicate direct selection of a field and selection via fragment spread' => [
		'type Query { a: String b: String }',
		[
			'Query.a' => 'Alice',
			'Query.b' => 'Bob',
		],
		'fragment F on Query { b } query { a b ...F }',
		[],
		[
			'data' => [
				'a' => 'Alice',
				'b' => 'Bob',
			],
		],
	],
	'valid operation with duplicate selection of a field via inline fragment' => [
		'type Query { a: String b: String }',
		[
			'Query.a' => 'Alice',
			'Query.b' => 'Bob',
		],
		'query { a ... { b b } }',
		[],
		[
			'data' => [
				'a' => 'Alice',
				'b' => 'Bob',
			],
		],
	],
	'valid operation with duplicate selection of a field via fragment spread' => [
		'type Query { a: String b: String }',
		[
			'Query.a' => 'Alice',
			'Query.b' => 'Bob',
		],
		'fragment F on Query { b b } query { a ...F }',
		[],
		[
			'data' => [
				'a' => 'Alice',
				'b' => 'Bob',
			],
		],
	],
	'valid operation with duplicate spread of a fragment' => [
		'type Query { a: String b: String }',
		[
			'Query.a' => 'Alice',
			'Query.b' => 'Bob',
		],
		'fragment F on Query { b } query { a ...F ...F }',
		[],
		[
			'data' => [
				'a' => 'Alice',
				'b' => 'Bob',
			],
		],
	],
	'valid operation with fragments spread exponentially many times' => [
		'type Query { a: String b: String }',
		[
			'Query.a' => 'Alice',
			'Query.b' => 'Bob',
		],
		'query { ...F0 } fragment F0 on Query { a ...F1 ...F1 } fragment F1 on Query { a ...F2 ...F2 } fragment F2 on Query { a ...F3 ...F3 } fragment F3 on Query { a ...F4 ...F4 } fragment F4 on Query { a ...F5 ...F5 } fragment F5 on Query { a ...F6 ...F6 } fragment F6 on Query { a ...F7 ...F7 } fragment F7 on Query { a ...F8 ...F8 } fragment F8 on Query { a ...F9 ...F9 } fragment F9 on Query { a ...F10 ...F10 } fragment F10 on Query { a ...F11 ...F11 } fragment F11 on Query { a ...F12 ...F12 } fragment F12 on Query { a ...F13 ...F13 } fragment F13 on Query { a ...F14 ...F14 } fragment F14 on Query { a ...F15 ...F15 } fragment F15 on Query { a ...F16 ...F16 } fragment F16 on Query { a ...F17 ...F17 } fragment F17 on Query { a ...F18 ...F18 } fragment F18 on Query { a ...F19 ...F19 } fragment F19 on Query { a ...F20 ...F20 } fragment F20 on Query { a ...F21 ...F21 } fragment F21 on Query { a ...F22 ...F22 } fragment F22 on Query { a ...F23 ...F23 } fragment F23 on Query { a ...F24 ...F24 } fragment F24 on Query { a ...F25 ...F25 } fragment F25 on Query { b }',
		[],
		[
			'data' => [
				'a' => 'Alice',
				'b' => 'Bob',
			],
		],
	],
	'valid operation with single field via fragment spread on interface type' => [
		'type O implements I { a: String } interface I { a: String } type Query { a: I }',
		[
			'O.a' => ResolveFromParent(),
			'Query.a' => [
				'type' => 'O',
				'a' => 'Alice',
			],
			'abstractTypeResolvers' => [
				'I' => ResolveAbstractTypeByField('type'),
			],
		],
		'fragment F on I { a } query { a { ...F } }',
		[],
		[
			'data' => [
				'a' => [
					'a' => 'Alice',
				],
			],
		],
	],
	'invalid operation with abstract type resolved to unknown type' => [
		'type O implements I { a: String } interface I { a: String } type Query { a: I }',
		[
			'O.a' => ShouldNotResolve(),
			'Query.a' => [
				'type' => 'O',
				'a' => 'Alice',
			],
			'abstractTypeResolvers' => [
				'I' => 'X',
			],
		],
		'fragment F on I { a } query { a { ...F } }',
		[],
		[
			'data' => [
				'a' => null,
			],
			'errors' => [
				[
					'message' => 'Abstract type I was incorrectly resolved to unknown type X',
					'path' => ['a'],
				],
			],
		],
	],
	'invalid operation with abstract type resolved to non-object type' => [
		'type O implements I { a: String } interface I { a: String } type Query { a: I }',
		[
			'O.a' => ShouldNotResolve(),
			'Query.a' => [
				'type' => 'O',
				'a' => 'Alice',
			],
			'abstractTypeResolvers' => [
				'I' => 'I',
			],
		],
		'fragment F on I { a } query { a { ...F } }',
		[],
		[
			'data' => [
				'a' => null,
			],
			'errors' => [
				[
					'message' => 'Abstract type I was incorrectly resolved to interface type I',
					'path' => ['a'],
				],
			],
		],
	],
	'valid operation with subselection resolved from parent' => [
		'type A { a: String } type Query { a: A }',
		[
			'A.a' => ResolveFromParent(),
			'Query.a' => [
				'a' => 'Alice',
			],
		],
		'query { a { a } }',
		[],
		[
			'data' => [
				'a' => [
					'a' => 'Alice',
				],
			],
		],
	],
	'valid operation with subselection resolved statically' => [
		'type A { a: String } type Query { a: A }',
		[
			'A.a' => 'Alice',
			'Query.a' => [],
		],
		'query { a { a } }',
		[],
		[
			'data' => [
				'a' => [
					'a' => 'Alice',
				],
			],
		],
	],
	'valid operation with single scalar list field' => [
		'type Query { a: [String] }',
		[
			'Query.a' => ['Alice', 'Bob', 'Charlie'],
		],
		'query { a }',
		[],
		[
			'data' => [
				'a' => [
					'Alice',
					'Bob',
					'Charlie',
				],
			],
		],
	],
	'valid operation with single list field with subselection resolved from parent' => [
		'type A { a: String } type Query { a: [A] }',
		[
			'A.a' => ResolveFromParent(),
			'Query.a' => [
				[
					'a' => 'Alice',
				],
				[
					'a' => 'Bob',
				],
				[
					'a' => 'Charlie',
				],
			],
		],
		'query { a { a } }',
		[],
		[
			'data' => [
				'a' => [
					[
						'a' => 'Alice',
					],
					[
						'a' => 'Bob',
					],
					[
						'a' => 'Charlie',
					],
				],
			],
		],
	],
	'valid operation with single list field with subselection resolved statically' => [
		'type A { a: String } type Query { a: [A] }',
		[
			'A.a' => 'Alice',
			'Query.a' => [
				[],
				[],
				[],
			],
		],
		'query { a { a } }',
		[],
		[
			'data' => [
				'a' => [
					[
						'a' => 'Alice',
					],
					[
						'a' => 'Alice',
					],
					[
						'a' => 'Alice',
					],
				],
			],
		],
	],
	'valid operation with list of non-nullables collapsed to null due to null item' => [
		'type Query { a: [String!] }',
		[
			'Query.a' => ['Alice', null, 'Charlie'],
		],
		'query { a }',
		[],
		[
			'data' => [
				'a' => null,
			],
			'errors' => [
				[
					'message' => 'Non-nullable item resolved to null',
					'path' => ['a', 1],
				],
			],
		],
	],
	'valid operation with object with non-nullable fields collapsed to null due to null field value' => [
		'type A { a: String! b: String! } type Query { a: A }',
		[
			'A.a' => 'Alice',
			'A.b' => null,
			'Query.a' => [],
		],
		'query { a { a b } }',
		[],
		[
			'data' => [
				'a' => null,
			],
			'errors' => [
				[
					'message' => 'Non-nullable field resolved to null',
					'path' => ['a', 'b'],
				],
			],
		],
	],
	'valid operation with root level object with non-nullable fields collapsed to null due to null field value' => [
		'type A { a: String! b: String! } type Query { a: A! }',
		[
			'A.a' => 'Alice',
			'A.b' => null,
			'Query.a' => [],
		],
		'query { a { a b } }',
		[],
		[
			'data' => null,
			'errors' => [
				[
					'message' => 'Non-nullable field resolved to null',
					'path' => ['a', 'b'],
				],
				[
					'message' => 'Non-nullable field resolved to null',
					'path' => ['a'],
				],
			],
		],
	],
	'valid operation with __typename field' => [
		'type A { a: String } type Query { a: A }',
		[
			'A.a' => 'Alice',
			'Query.a' => [],
		],
		'query { a { a __typename } __typename }',
		[],
		[
			'data' => [
				'a' => [
					'a' => 'Alice',
					'__typename' => 'A',
				],
				'__typename' => 'Query',
			],
		],
	],
	'valid operation with field predictably failing' => [
		'type Query { a: String }',
		[
			'Query.a' => FailResolvingWithMessage('Yikes!'),
		],
		'query { a }',
		[],
		[
			'data' => [
				'a' => null,
			],
			'errors' => [
				[
					'message' => 'Yikes!',
					'path' => ['a'],
				],
			],
		],
	],
	'invalid operation with list field resolved with non-iterable value' => [
		'type Query { a: [String] }',
		[
			'Query.a' => 'Alice',
		],
		'query { a }',
		[],
		[
			'data' => [
				'a' => null,
			],
			'errors' => [
				[
					'message' => 'List resolved to non-iterable value',
					'path' => ['a'],
				],
			],
		],
	],
];
