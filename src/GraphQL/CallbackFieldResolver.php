<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL;


/**
 * @template TResolvedValue
 * @implements FieldResolver<mixed, TResolvedValue>
 */
final class CallbackFieldResolver implements FieldResolver
{

	/** @var callable(mixed, FieldSelection): TResolvedValue */
	private $callback;



	/**
	 * @param callable(mixed, FieldSelection): TResolvedValue $callback
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
