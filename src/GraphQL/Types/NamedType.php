<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\Types;


final class NamedType implements Type
{

	public function __construct(
		public readonly string $name,
	) {}



	public function format(): string
	{
		return $this->name;
	}



	public function getNamedType(): string
	{
		return $this->name;
	}



	public function getWrappedType(): null
	{
		return null;
	}

}
