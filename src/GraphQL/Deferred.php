<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL;

use GuzzleHttp;


/**
 * @template TValue
 */
final class Deferred
{

	/** @var callable(): TValue */
	private $callback;



	/**
	 * @param callable(): TValue $callback
	 */
	public function __construct(callable $callback)
	{
		$this->callback = $callback;
	}



	/**
	 * @return TValue
	 */
	public function execute(): mixed
	{
		return ($this->callback)();
	}



	public function createPromise(): GuzzleHttp\Promise\Promise
	{
		$promise = new GuzzleHttp\Promise\Promise(
			function () use (& $promise): void {
				$promise->resolve(
					$this->execute(),
				);
			},
		);

		return $promise;
	}

}
