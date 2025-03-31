<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\Builtin;

use Vojtechdobes\GraphQL;


/**
 * @implements GraphQL\ScalarImplementation<float, float>
 */
final class FloatScalarImplementation implements GraphQL\ScalarImplementation
{

	public function parseLiteralValue(GraphQL\Values\Value $value): float
	{
		if (!$value instanceof GraphQL\Values\FloatValue) {
			throw new GraphQL\Exceptions\CannotParseScalarLiteralValueException();
		}

		return $value->value;
	}



	public function parseRuntimeValue(mixed $value): mixed
	{
		if (is_float($value) === false) {
			throw new GraphQL\Exceptions\CannotParseScalarRuntimeValueException();
		}

		return $value;
	}



	public function serialize(mixed $value): mixed
	{
		return $value;
	}

}
