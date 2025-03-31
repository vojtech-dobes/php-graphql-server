<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\TypeSystem;

use Vojtechdobes\GraphQL;


final class ScalarTypeDefinition implements TypeDefinition
{

	public TypeKind $kind {

		get {
			return TypeKind::Scalar;
		}

	}



	/**
	 * @param list<GraphQL\Executable\Directive<GraphQL\Values\Value<mixed>>> $directives
	 */
	public function __construct(
		public readonly string $name,
		public readonly ?string $description,
		public readonly array $directives,
	) {}

}
