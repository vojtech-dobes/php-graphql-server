<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\Exceptions;

use RuntimeException;
use Throwable;


final class FailedToResolveFieldException extends RuntimeException
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
