<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\Types;


final class NonNullType implements Type
{

	public function __construct(
		public readonly Type $type,
	) {}



	public function format(): string
	{
		return $this->type->format() . '!';
	}



	public function getNamedType(): string
	{
		return $this->type->getNamedType();
	}



	public function getWrappedType(): Type
	{
		return $this->type;
	}

}
