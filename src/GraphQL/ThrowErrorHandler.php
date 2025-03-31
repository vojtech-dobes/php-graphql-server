<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL;

use Throwable;


final class ThrowErrorHandler implements ErrorHandler
{

	public function handleFieldResolverError(Throwable $e, FieldSelection $fieldSelection): void
	{
		throw $e;
	}



	public function handleAbstractTypeResolverError(Throwable $e, FieldSelection $fieldSelection, mixed $objectValue): void
	{
		throw $e;
	}

}
