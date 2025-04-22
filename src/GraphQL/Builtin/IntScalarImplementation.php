<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\Builtin;

use Vojtechdobes\GraphQL;


/**
 * @implements GraphQL\ScalarImplementation<int, int>
 */
final class IntScalarImplementation implements GraphQL\ScalarImplementation
{

	public function parseLiteralValue(GraphQL\Values\Value $value): int
	{
		if (!$value instanceof GraphQL\Values\IntValue) {
			throw new GraphQL\Exceptions\CannotParseScalarLiteralValueException();
		}

		return $value->value;
	}



	public function parseRuntimeValue(mixed $value): mixed
	{
		if (is_int($value) === false) {
			throw new GraphQL\Exceptions\CannotParseScalarRuntimeValueException();
		}

		return $value;
	}



	public function serialize(mixed $value): mixed
	{
		return $value;
	}

}
