<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\Values;


/**
 * @implements Value<null>
 */
final class NullValue implements Value
{

	public function __construct(
		public readonly bool $isExplicit,
	) {}



	public function getRuntimeValue(): null
	{
		return null;
	}



	public function getLabel(): string
	{
		return 'null';
	}



	public function toString(): string
	{
		return 'null';
	}

}
