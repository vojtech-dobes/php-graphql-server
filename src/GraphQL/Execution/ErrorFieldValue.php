<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\Execution;

use Vojtechdobes\GraphQL;


/**
 * @implements FieldValue<null>
 */
final class ErrorFieldValue implements FieldValue
{

	public function __construct(
		private readonly GraphQL\Error $error,
	) {}



	public function getData(
		GraphQL\Errors $fieldErrors,
		ResponsePath $responsePath,
	): null
	{
		$fieldErrors->addError(
			$this->error->withResponsePath($responsePath),
		);

		return null;
	}

}
