<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\TypeSystem;

use JiriPudil;
use Vojtechdobes\GraphQL;


#[JiriPudil\SealedClasses\Sealed(permits: [
	EnumTypeDefinition::class,
	InputObjectTypeDefinition::class,
	InterfaceTypeDefinition::class,
	ObjectTypeDefinition::class,
	ScalarTypeDefinition::class,
	UnionTypeDefinition::class,
])]
interface TypeDefinition
{

	public ?string $description { get; }

	/** @var list<GraphQL\Executable\Directive<GraphQL\Values\Value<mixed>>> */
	public array $directives { get; }

	/** @var TypeKind::Enum|TypeKind::InputObject|TypeKind::Interface_|TypeKind::Object_|TypeKind::Scalar|TypeKind::Union */
	public TypeKind $kind { get; }
	public string $name { get; }

}
