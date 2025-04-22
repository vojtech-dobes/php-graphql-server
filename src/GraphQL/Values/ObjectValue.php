<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\Values;


/**
 * @implements Value<array<string, mixed>>
 */
final class ObjectValue implements Value
{

	/**
	 * @param list<ObjectValueField> $fields
	 */
	public function __construct(
		public readonly array $fields,
	) {}



	public function getRuntimeValue(): array
	{
		$result = [];

		foreach ($this->fields as $field) {
			$result[$field->name] = $field->value->getRuntimeValue();
		}

		return $result;
	}



	public function getLabel(): string
	{
		return 'object';
	}



	public function toString(): string
	{
		return '{' . implode(', ', array_map(
			static fn ($field) => sprintf(
				'%s: %s',
				$field->name,
				$field->value->toString(),
			),
			$this->fields,
		)) . '}';
	}



	/**
	 * @return Value<mixed>
	 */
	public function getFieldValue(string $fieldName): Value
	{
		foreach ($this->fields as $field) {
			if ($field->name === $fieldName) {
				return $field->value;
			}
		}

		return new NullValue(isExplicit: false);
	}



	/**
	 * @return list<string>
	 */
	public function listDuplicateFields(): array
	{
		return array_keys(
			array_filter(
				array_count_values(
					array_map(
						static fn ($field) => $field->name,
						$this->fields,
					),
				),
				static fn ($count) => $count > 1,
			),
		);
	}

}
