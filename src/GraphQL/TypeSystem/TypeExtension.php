<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\TypeSystem;


final class TypeExtension
{

	public function __construct(
		public readonly TypeDefinition $typeDefinition,
	) {}

}
