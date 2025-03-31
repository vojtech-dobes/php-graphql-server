<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL;


/**
 * @template TObjectValue
 * @template TResolvedValue
 * @template-covariant TArguments of array<string, mixed> = array{}
 * @template-covariant TContext = null
 */
interface FieldResolver
{

	/**
	 * @param TObjectValue $objectValue
	 * @param FieldSelection<contravariant TArguments, contravariant TContext> $field
	 * @return TResolvedValue
	 * @throws Exceptions\FailedToResolveFieldException
	 */
	function resolveField(mixed $objectValue, FieldSelection $field): mixed;

}
