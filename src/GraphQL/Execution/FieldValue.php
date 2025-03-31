<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\Execution;

use Vojtechdobes\GraphQL;


/**
 * @template TData
 */
interface FieldValue
{

	/**
	 * @return TData
	 */
	function getData(
		GraphQL\Errors $fieldErrors,
		ResponsePath $responsePath,
	);

}
