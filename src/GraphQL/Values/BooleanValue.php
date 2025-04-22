<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\Values;


/**
 * @implements Value<bool>
 */
final class BooleanValue implements Value
{

	public function __construct(
		public readonly bool $value,
	) {}



	public function getRuntimeValue(): bool
	{
		return $this->value;
	}



	public function getLabel(): string
	{
		return 'boolean';
	}



	public function toString(): string
	{
		return match ($this->value) {
			true => 'true',
			false => 'false',
		};
	}

}
