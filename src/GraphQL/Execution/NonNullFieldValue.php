<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\Execution;

use Vojtechdobes\GraphQL;


/**
 * @implements FieldValue<mixed>
 */
final class NonNullFieldValue implements FieldValue
{

	/**
	 * @param FieldValue<mixed> $value
	 */
	public function __construct(
		private readonly FieldValue $value,
	) {}



	public function getData(
		GraphQL\Errors $fieldErrors,
		ResponsePath $responsePath,
	): mixed
	{
		$data = $this->value->getData(
			$fieldErrors,
			$responsePath,
		);

		if ($data === null && !$this->value instanceof ErrorFieldValue) {
			$fieldErrors->addError(
				new GraphQL\Error(
					is_string($responsePath->segment)
						? 'Non-nullable field resolved to null'
						: 'Non-nullable item resolved to null',
					responsePath: $responsePath,
				),
			);
		}

		return $data;
	}

}
