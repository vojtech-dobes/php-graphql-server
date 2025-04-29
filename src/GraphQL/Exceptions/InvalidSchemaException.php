<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\Exceptions;

use RuntimeException;
use Throwable;
use Vojtechdobes\GraphQL;


abstract class InvalidSchemaException extends RuntimeException
{

	/**
	 * @param non-empty-list<GraphQL\Error> $errors
	 */
	final public function __construct(
		public readonly array $errors,
		?Throwable $previous = null,
	)
	{
		parent::__construct(
			sprintf(
				"Errors:\n- %s",
				implode("\n -", array_map(
					static fn ($error) => $error->message,
					$errors,
				)),
			),
			0,
			$previous,
		);
	}

}
