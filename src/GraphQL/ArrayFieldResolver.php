<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL;


/**
 * @implements FieldResolver<array<string, mixed>, mixed>
 */
final class ArrayFieldResolver implements FieldResolver
{

	public function resolveField(mixed $arrayValue, FieldSelection $field): mixed
	{
		return $arrayValue[$field->name];
	}

}
