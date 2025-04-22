<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\Execution;

use Vojtechdobes\GraphQL;


/**
 * @implements FieldValue<mixed>
 */
final class ScalarFieldValue implements FieldValue
{

	public function __construct(
		private readonly mixed $value,
	) {}



	public function getData(
		GraphQL\Errors $fieldErrors,
		ResponsePath $responsePath,
	): mixed
	{
		return $this->value;
	}

}
