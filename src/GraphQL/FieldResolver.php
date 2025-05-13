<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL;


/**
 * @template TObjectValue
 * @template TResolvedValue
 * @template TArguments of array<string, mixed> = array{}
 * @template TContext = null
 */
interface FieldResolver
{

	/**
	 * @param TObjectValue $objectValue
	 * @param FieldSelection<TArguments, TContext> $field
	 * @return TResolvedValue
	 * @throws Exceptions\FailedToResolveFieldException
	 */
	function resolveField(mixed $objectValue, FieldSelection $field): mixed;

}
