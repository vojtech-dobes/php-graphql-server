<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL;


/**
 * @implements AbstractTypeResolver<object>
 */
final class ClassNameAbstractTypeResolver implements AbstractTypeResolver
{

	public function resolveAbstractType(mixed $objectValue): string
	{
		return $objectValue::class;
	}

}
