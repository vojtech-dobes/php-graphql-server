<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\Types;


final class ListType implements Type
{

	public function __construct(
		public readonly Type $itemType,
	) {}



	public function format(): string
	{
		return '[' . $this->itemType->format() . ']';
	}



	public function getNamedType(): string
	{
		return $this->itemType->getNamedType();
	}



	public function getWrappedType(): Type
	{
		return $this->itemType;
	}

}
