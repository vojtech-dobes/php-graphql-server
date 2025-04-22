<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\Exceptions;

use RuntimeException;
use Throwable;


final class CannotParseDocumentException extends RuntimeException
{

	/**
	 * @param non-empty-list<string> $errors
	 */
	final public function __construct(
		public readonly array $errors,
		?Throwable $previous = null,
	)
	{
		parent::__construct("Errors:\n- " . implode("\n -", $errors), 0, $previous);
	}

}
