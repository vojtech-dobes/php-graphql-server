<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL;


/**
 * @implements ContextFactory<null>
 */
final class NullContextFactory implements ContextFactory
{

	public function createContext(): mixed
	{
		return null;
	}

}
