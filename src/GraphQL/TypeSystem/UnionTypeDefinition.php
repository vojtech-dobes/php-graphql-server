<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\TypeSystem;

use Vojtechdobes\GraphQL;


final class UnionTypeDefinition implements TypeDefinition, TypeWithFieldSelection
{

	public TypeKind $kind {

		get {
			return TypeKind::Union;
		}

	}



	/**
	 * @param list<GraphQL\Executable\Directive<GraphQL\Values\Value<mixed>>> $directives
	 * @param list<string> $possibleTypes
	 */
	public function __construct(
		public readonly string $name,
		public readonly ?string $description,
		public readonly array $directives,
		public readonly array $possibleTypes,
	) {}

}
