<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\Exceptions;

use RuntimeException;


final class CannotSerializeScalarValueException extends RuntimeException
{

	/**
	 * @param array<string, mixed>|null $extensions
	 */
	public function __construct(
		string $message,
		public readonly ?array $extensions = null,
	)
	{
		parent::__construct($message);
	}

}
