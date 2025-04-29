<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\Exceptions;

use RuntimeException;
use Vojtechdobes\GraphQL;


final class CannotResolveVariableRuntimeValuesException extends RuntimeException
{

	/**
	 * @param non-empty-list<GraphQL\Error> $errors
	 */
	public function __construct(
		public readonly array $errors,
	)
	{
		parent::__construct();
	}

}
