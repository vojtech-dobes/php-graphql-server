<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\Values;


/**
 * @implements Value<string>
 */
final class StringValue implements Value
{

	public function __construct(
		public readonly string $value,
	) {}



	public function getRuntimeValue(): string
	{
		return $this->value;
	}



	public function getLabel(): string
	{
		return 'string';
	}



	public function toString(): string
	{
		return '"' . strtr($this->value, [
			'"' => '\"',
		]) . '"';
	}

}
