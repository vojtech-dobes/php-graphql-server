<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\Executable;

use Vojtechdobes\GraphQL;


final class Operation
{

	public function __construct(
		public readonly OperationDefinition $definition,
		public readonly GraphQL\TypeSystem\ObjectTypeDefinition $rootType,
	) {}

}
