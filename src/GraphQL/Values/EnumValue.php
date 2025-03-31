<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\Values;


/**
 * @template TEnumValue of string
 * @implements Value<TEnumValue>
 */
final class EnumValue implements Value
{

	/**
	 * @param TEnumValue $value
	 */
	public function __construct(
		public readonly string $value,
	) {}



	public function getRuntimeValue(): string
	{
		return $this->value;
	}



	public function getLabel(): string
	{
		return 'enum';
	}



	public function toString(): string
	{
		return $this->value;
	}

}
