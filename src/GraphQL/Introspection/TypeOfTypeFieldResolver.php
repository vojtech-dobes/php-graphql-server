<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\Introspection;

use Vojtechdobes\GraphQL;


/**
 * @implements GraphQL\FieldResolver<
 *   GraphQL\Types\Type,
 *   GraphQL\Types\Type|null,
 * >
 */
final class TypeOfTypeFieldResolver implements GraphQL\FieldResolver
{

	public function resolveField(mixed $objectValue, GraphQL\FieldSelection $field): mixed
	{
		return $objectValue->getWrappedType();
	}

}
