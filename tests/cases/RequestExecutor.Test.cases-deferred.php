<?php declare(strict_types=1);

require_once __DIR__ . '/../../src/GraphQL/Deferred.php';

class Buffer
{

	/** @var list<string> */
	private static array $buffer = [];

	public static function add(string $item): void
	{
		self::$buffer[] = $item;
	}

	public static function export(): string
	{
		return implode('', self::$buffer);
	}

}

/**
 * @return callable(mixed, Vojtechdobes\GraphQL\FieldSelection): Vojtechdobes\GraphQL\Deferred<string>
 */
function ResolveWithBuffer(): callable {
	return static function ($parent, Vojtechdobes\GraphQL\FieldSelection $fieldSelection): Vojtechdobes\GraphQL\Deferred {
		Buffer::add($fieldSelection->name);

		return new Vojtechdobes\GraphQL\Deferred(
			static fn () => Buffer::export(),
		);
	};
}

return [
	'2 fields using buffer' => [
		'type Query { a: String b: String }',
		[
			'Query.a' => ResolveWithBuffer(),
			'Query.b' => ResolveWithBuffer(),
		],
		'query { a b }',
		[],
		[
			'data' => [
				'a' => 'ab',
				'b' => 'ab',
			],
		],
	],
	'3 fields using buffer' => [
		'type Query { a: String b: String c: String }',
		[
			'Query.a' => ResolveWithBuffer(),
			'Query.b' => ResolveWithBuffer(),
			'Query.c' => ResolveWithBuffer(),
		],
		'query { a b c }',
		[],
		[
			'data' => [
				'a' => 'abc',
				'b' => 'abc',
				'c' => 'abc',
			],
		],
	],
	'fields using buffer with deeper level next capture higher level fields' => [
		'type O { d: String e: String } type Query { a: String b: String c: O }',
		[
			'O.d' => ResolveWithBuffer(),
			'O.e' => ResolveWithBuffer(),
			'Query.a' => ResolveWithBuffer(),
			'Query.b' => ResolveWithBuffer(),
			'Query.c' => [],
		],
		'query { a b c { d e } }',
		[],
		[
			'data' => [
				'a' => 'abde',
				'b' => 'abde',
				'c' => [
					'd' => 'abde',
					'e' => 'abde',
				],
			],
		],
	],
	'fields using buffer with deeper level first capture higher level fields' => [
		'type O { d: String e: String } type Query { a: String b: String c: O }',
		[
			'O.d' => ResolveWithBuffer(),
			'O.e' => ResolveWithBuffer(),
			'Query.a' => ResolveWithBuffer(),
			'Query.b' => ResolveWithBuffer(),
			'Query.c' => [],
		],
		'query { c { d e } a b }',
		[],
		[
			'data' => [
				'c' => [
					'd' => 'deab',
					'e' => 'deab',
				],
				'a' => 'deab',
				'b' => 'deab',
			],
		],
	],
];
