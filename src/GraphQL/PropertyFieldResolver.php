<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL;


/**
 * @implements FieldResolver<object, mixed>
 */
final class PropertyFieldResolver implements FieldResolver
{

	public function resolveField(mixed $objectValue, FieldSelection $field): mixed
	{
		return $objectValue->{$field->name};
	}

}
