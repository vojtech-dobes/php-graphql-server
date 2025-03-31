<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\Introspection;

use Vojtechdobes\GraphQL;


/**
 * @implements GraphQL\FieldResolver<
 *   GraphQL\TypeSystem\Schema,
 *   array<GraphQL\TypeSystem\DirectiveDefinition>,
 * >
 */
final class SchemaDirectivesFieldResolver implements GraphQL\FieldResolver
{

	public function resolveField(mixed $objectValue, GraphQL\FieldSelection $field): mixed
	{
		return $objectValue->getDirectiveDefinitions();
	}

}
