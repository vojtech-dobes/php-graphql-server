<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL;


/**
 * @implements AbstractTypeResolver<mixed>
 */
final class CallbackAbstractTypeResolver implements AbstractTypeResolver
{

	/** @var callable(mixed): string */
	private $callback;



	/**
	 * @param callable(mixed): string $callback
	 */
	public function __construct(
		callable $callback,
	)
	{
		$this->callback = $callback;
	}



	public function resolveAbstractType(mixed $objectValue): string
	{
		return ($this->callback)($objectValue);
	}

}
