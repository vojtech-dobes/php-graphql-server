<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\TypeSystem;

use Vojtechdobes\GraphQL;


final class InputObjectTypeDefinition implements TypeDefinition
{

	public TypeKind $kind {

		get {
			return TypeKind::InputObject;
		}

	}



	/**
	 * @param list<GraphQL\Executable\Directive<GraphQL\Values\Value<mixed>>> $directives
	 * @param list<InputValueDefinition> $fields
	 */
	public function __construct(
		public readonly string $name,
		public readonly ?string $description,
		public readonly array $directives,
		public readonly array $fields,
	) {}

}
