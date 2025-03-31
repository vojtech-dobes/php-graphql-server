<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\Values;


/**
 * @implements Value<float>
 */
final class FloatValue implements Value
{

	public function __construct(
		public readonly float $value,
	) {}



	public function getRuntimeValue(): float
	{
		return $this->value;
	}



	public function getLabel(): string
	{
		return 'float';
	}



	public function toString(): string
	{
		return (string) $this->value;
	}

}
