<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\Exceptions;

use RuntimeException;


abstract class InvalidExecutableDocumentException extends RuntimeException
{

	/**
	 * @param non-empty-list<string> $errors
	 */
	final public function __construct(
		public readonly array $errors,
	)
	{
		parent::__construct("Errors:\n- " . implode("\n -", $errors));
	}

}
