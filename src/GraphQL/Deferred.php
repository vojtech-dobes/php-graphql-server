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

	private ?GuzzleHttp\Promise\Promise $promise = null;



	/**
	 * @param callable(): TValue $callback
	 */
	public function __construct(callable $callback)
	{
		$this->callback = $callback;
	}



	/**
	 * @template TChainValue
	 * @param callable(TValue): TChainValue $callback
	 * @return self<TChainValue>
	 */
	public function chain(callable $callback): self
	{
		return new Deferred(
			fn () => $callback($this->createPromise()->wait()),
		);
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
		$this->promise ??= new GuzzleHttp\Promise\Promise(
			function (): void {
				$this->promise->resolve(
					$this->execute(),
				);
			},
		);

		return $this->promise;
	}

}
