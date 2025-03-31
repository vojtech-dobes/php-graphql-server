<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\TypeSystem;

use JiriPudil;


#[JiriPudil\SealedClasses\Sealed(permits: [
	InterfaceTypeDefinition::class,
	ObjectTypeDefinition::class,
	UnionTypeDefinition::class,
])]
interface TypeWithFieldSelection
{

	/** @var TypeKind::Interface_|TypeKind::Object_|TypeKind::Union */
	public TypeKind $kind { get; }
	public string $name { get; }

}
