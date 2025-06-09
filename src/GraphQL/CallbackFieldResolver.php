<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL;


/**
 * @template TResolvedValue
 * @template TArguments of array<string, mixed> = array{}
 * @template TContext = null
 * @implements FieldResolver<mixed, TResolvedValue, TArguments, TContext>
 */
final class CallbackFieldResolver implements FieldResolver
{

	/** @var callable(mixed, FieldSelection<TArguments, TContext>): TResolvedValue */
	private mixed $callback;



	/**
	 * @param callable(mixed, FieldSelection<TArguments, TContext>): TResolvedValue $callback
	 */
	public function __construct(
		callable $callback,
	)
	{
		$this->callback = $callback;
	}



	public function resolveField(mixed $objectValue, FieldSelection $field): mixed
	{
		return ($this->callback)($objectValue, $field);
	}

}
