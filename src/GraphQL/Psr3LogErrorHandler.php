<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL;

use Psr;
use Throwable;


final class Psr3LogErrorHandler implements ErrorHandler
{

	/**
	 * @param Psr\Log\LogLevel::* $level
	 */
	public function __construct(
		private readonly Psr\Log\LoggerInterface $logger,
		private readonly string $level = Psr\Log\LogLevel::ERROR,
	) {}



	public function handleFieldResolverError(Throwable $e, FieldSelection $fieldSelection): void
	{
		$this->logger->log($this->level, 'GraphQL failed to resolve field', [
			'exception' => $e,
		]);
	}



	public function handleAbstractTypeResolverError(Throwable $e, FieldSelection $fieldSelection, mixed $objectValue): void
	{
		$this->logger->log($this->level, 'GraphQL failed to resolve abstract type', [
			'exception' => $e,
		]);
	}



	public function handleSerializeScalarError(Throwable $e, FieldSelection $fieldSelection, mixed $scalarValue): void
	{
		$this->logger->log($this->level, 'GraphQL failed to serialize scalar type', [
			'exception' => $e,
		]);
	}

}
