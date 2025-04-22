<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL;

use GuzzleHttp;


final class Deferred
{

	/** @var callable(): mixed */
	private $callback;



	public function __construct(callable $callback)
	{
		$this->callback = $callback;
	}



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
