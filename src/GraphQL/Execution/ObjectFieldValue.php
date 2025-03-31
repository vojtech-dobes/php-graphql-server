<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\Execution;

use Vojtechdobes\GraphQL;


/**
 * @implements FieldValue<array<string, mixed>|object{}|null>
 */
final class ObjectFieldValue implements FieldValue
{

	/**
	 * @param array<string, FieldValue<mixed>> $fieldValues
	 */
	public function __construct(
		private readonly bool $areAllFieldsNotNullable,
		private readonly array $fieldValues,
	) {}



	public function getData(
		GraphQL\Errors $fieldErrors,
		ResponsePath $responsePath,
	): array|object|null
	{
		$result = [];
		$hasNullField = false;

		foreach ($this->fieldValues as $fieldResponseKey => $fieldValue) {
			$fieldData = $fieldValue->getData(
				$fieldErrors,
				$responsePath->addField($fieldResponseKey),
			);

			if ($hasNullField === false && $fieldData === null) {
				$hasNullField = true;
			}

			$result[$fieldResponseKey] = $fieldData;
		}

		if ($this->areAllFieldsNotNullable && $hasNullField) {
			return null;
		}

		if ($result === []) {
			return (object) [];
		}

		return $result;
	}

}
