<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL;


/**
 * @template TObjectValue
 */
interface AbstractTypeResolver
{

	/**
	 * Returned Object Type name must be part of the Schema, otherwise field
	 * resolution will fail with field error.
	 *
	 * @param TObjectValue $objectValue
	 */
	function resolveAbstractType(mixed $objectValue): string;

}
