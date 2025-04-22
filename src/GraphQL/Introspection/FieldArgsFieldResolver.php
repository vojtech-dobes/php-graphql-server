<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\Introspection;

use Vojtechdobes\GraphQL;


/**
 * @implements GraphQL\FieldResolver<
 *   GraphQL\TypeSystem\FieldDefinition,
 *   list<GraphQL\TypeSystem\InputValueDefinition>,
 * >
 */
final class FieldArgsFieldResolver implements GraphQL\FieldResolver
{

	public function resolveField(mixed $objectValue, GraphQL\FieldSelection $field): mixed
	{
		return $objectValue->argumentDefinitions;
	}

}
