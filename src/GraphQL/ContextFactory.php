<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL;


/**
 * @template TContext
 */
interface ContextFactory
{

	/**
	 * @return TContext
	 */
	function createContext(): mixed;

}
