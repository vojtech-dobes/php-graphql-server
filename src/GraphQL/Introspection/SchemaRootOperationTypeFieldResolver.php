<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\Introspection;

use Vojtechdobes\GraphQL;


/**
 * @implements GraphQL\FieldResolver<
 *   GraphQL\TypeSystem\Schema,
 *   GraphQL\Types\NamedType|null,
 * >
 */
final class SchemaRootOperationTypeFieldResolver implements GraphQL\FieldResolver
{

	public function __construct(
		private readonly GraphQL\OperationType $operationType,
	) {}



	public function resolveField(mixed $objectValue, GraphQL\FieldSelection $field): mixed
	{
		$rootTypeDefinition = $objectValue->getRootOperationTypeDefinition($this->operationType);

		if ($rootTypeDefinition === null) {
			return null;
		}

		return new GraphQL\Types\NamedType($rootTypeDefinition->name);
	}

}
