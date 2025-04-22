<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\Values;


/**
 * @template TItemValue of Value
 * @implements Value<list<TItemValue>>
 */
final class ListValue implements Value
{

	/**
	 * @param list<TItemValue> $items
	 */
	public function __construct(
		public readonly array $items,
	) {}



	public function getRuntimeValue(): array
	{
		return array_map(
			static fn ($item) => $item->getRuntimeValue(),
			$this->items,
		);
	}



	public function getLabel(): string
	{
		return 'list';
	}



	public function toString(): string
	{
		return '[' . implode(', ', array_map(
			static fn ($item) => $item->toString(),
			$this->items,
		)) . ']';
	}

}
