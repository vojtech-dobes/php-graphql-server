<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL;


/**
 * @implements FieldResolver<object, mixed>
 */
final class GetterFieldResolver implements FieldResolver
{

	public function resolveField(mixed $objectValue, FieldSelection $field): mixed
	{
		return $objectValue->{'get' . ucfirst($field->name)}();
	}

}
