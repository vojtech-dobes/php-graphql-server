<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\Execution;

use Vojtechdobes\GraphQL;


/**
 * @implements FieldValue<list<mixed>|null>
 */
final class ListFieldValue implements FieldValue
{

	/**
	 * @param list<FieldValue<mixed>> $itemValues
	 */
	public function __construct(
		private readonly bool $isItemNotNullable,
		private readonly array $itemValues,
	) {}



	public function getData(
		GraphQL\Errors $fieldErrors,
		ResponsePath $responsePath,
	): ?array
	{
		$result = [];
		$hasNullItem = false;

		foreach ($this->itemValues as $i => $itemValue) {
			$itemData = $itemValue->getData(
				$fieldErrors,
				$responsePath->addItemIndex($i),
			);

			if ($hasNullItem === false && $itemData === null) {
				$hasNullItem = true;
			}

			$result[] = $itemData;
		}

		if ($this->isItemNotNullable && $hasNullItem) {
			return null;
		}

		return $result;
	}

}
