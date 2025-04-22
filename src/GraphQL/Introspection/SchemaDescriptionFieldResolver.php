<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\Introspection;

use Vojtechdobes\GraphQL;


/**
 * @implements GraphQL\FieldResolver<
 *   GraphQL\TypeSystem\Schema,
 *   string|null,
 * >
 */
final class SchemaDescriptionFieldResolver implements GraphQL\FieldResolver
{

	public function resolveField(mixed $objectValue, GraphQL\FieldSelection $field): mixed
	{
		return $objectValue->description;
	}

}
