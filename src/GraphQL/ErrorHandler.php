<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL;

use Throwable;


interface ErrorHandler
{

	/**
	 * @param FieldSelection<array<string, mixed>> $fieldSelection
	 */
	function handleFieldResolverError(
		Throwable $e,
		FieldSelection $fieldSelection,
	): void;



	function handleAbstractTypeResolverError(
		Throwable $e,
		FieldSelection $fieldSelection,
		mixed $objectValue,
	): void;



	function handleSerializeScalarError(
		Throwable $e,
		FieldSelection $fieldSelection,
		mixed $scalarValue,
	): void;

}
