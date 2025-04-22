<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\Values;


/**
 * @implements Value<int>
 */
final class IntValue implements Value
{

	public function __construct(
		public readonly int $value,
	) {}



	public function getRuntimeValue(): int
	{
		return $this->value;
	}



	public function getLabel(): string
	{
		return 'integer';
	}



	public function toString(): string
	{
		return (string) $this->value;
	}

}
