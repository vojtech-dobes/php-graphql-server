<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\Builtin;

use Vojtechdobes\GraphQL;


/**
 * @implements GraphQL\ScalarImplementation<bool, bool>
 */
final class BooleanScalarImplementation implements GraphQL\ScalarImplementation
{

	public function parseLiteralValue(GraphQL\Values\Value $value): bool
	{
		if (!$value instanceof GraphQL\Values\BooleanValue) {
			throw new GraphQL\Exceptions\CannotParseScalarLiteralValueException();
		}

		return $value->value;
	}



	public function parseRuntimeValue(mixed $value): mixed
	{
		if (is_bool($value) === false) {
			throw new GraphQL\Exceptions\CannotParseScalarRuntimeValueException();
		}

		return $value;
	}



	public function serialize(mixed $value): mixed
	{
		return $value;
	}

}
