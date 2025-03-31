<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\Exceptions;

use RuntimeException;


final class UnknownOperationException extends RuntimeException
{

	public function __construct(
		public readonly ?string $operationName,
		string $message,
	)
	{
		parent::__construct($message);
	}

}
