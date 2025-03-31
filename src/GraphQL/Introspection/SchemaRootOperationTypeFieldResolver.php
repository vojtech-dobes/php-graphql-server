<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\Introspection;

use Vojtechdobes\GraphQL;


/**
 * @implements GraphQL\FieldResolver<
 *   GraphQL\TypeSystem\Schema,
 *   GraphQL\TypeSystem\ObjectTypeDefinition|null,
 * >
 */
final class SchemaRootOperationTypeFieldResolver implements GraphQL\FieldResolver
{

	public function __construct(
		private readonly GraphQL\OperationType $operationType,
	) {}



	public function resolveField(mixed $objectValue, GraphQL\FieldSelection $field): mixed
	{
		return $objectValue->getRootOperationTypeDefinition($this->operationType);
	}

}
