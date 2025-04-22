<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\TypeSystem;


/**
 * @template TItem
 * @implements Registry<TItem>
 */
final class StaticRegistry implements Registry
{

	/**
	 * @param array<string, TItem> $items
	 */
	public function __construct(
		private readonly array $items,
	) {}



	public function getItem(string $name): mixed
	{
		return $this->items[$name];
	}



	public function getItemOrNull(string $name): mixed
	{
		return $this->items[$name] ?? null;
	}



	public function getAll(): array
	{
		return $this->items;
	}

}
