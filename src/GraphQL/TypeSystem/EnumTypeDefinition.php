<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\TypeSystem;

use Vojtechdobes\GraphQL;


final class EnumTypeDefinition implements TypeDefinition
{

	public TypeKind $kind {

		get {
			return TypeKind::Enum;
		}

	}



	/**
	 * @param list<GraphQL\Executable\Directive<GraphQL\Values\Value<mixed>>> $directives
	 * @param list<EnumValueDefinition> $enumValues
	 */
	public function __construct(
		public readonly string $name,
		public readonly ?string $description,
		public readonly array $directives,
		public readonly array $enumValues,
	) {}

}
