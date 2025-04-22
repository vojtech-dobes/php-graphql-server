<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\TypeSystem;


/**
 * @template TItem
 */
interface Registry
{

	/**
	 * @return TItem
	 */
	function getItem(string $name): mixed;



	/**
	 * @return TItem|null
	 */
	function getItemOrNull(string $name): mixed;



	/**
	 * @return array<string, TItem>
	 */
	function getAll(): array;

}
