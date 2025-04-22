<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\Types;

use JiriPudil;


#[JiriPudil\SealedClasses\Sealed(permits: [
	ListType::class,
	NamedType::class,
	NonNullType::class,
])]
interface Type
{

	function format(): string;



	function getNamedType(): string;



	function getWrappedType(): ?Type;

}
