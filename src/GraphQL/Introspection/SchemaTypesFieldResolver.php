<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\Introspection;

use Vojtechdobes\GraphQL;


/**
 * @implements GraphQL\FieldResolver<
 *   GraphQL\TypeSystem\Schema,
 *   array<GraphQL\Types\NamedType>,
 * >
 */
final class SchemaTypesFieldResolver implements GraphQL\FieldResolver
{

	public function resolveField(mixed $objectValue, GraphQL\FieldSelection $field): mixed
	{
		return array_map(
			static fn ($typeDefinition) => new GraphQL\Types\NamedType($typeDefinition->name),
			$objectValue->getTypeDefinitions(),
		);
	}

}
