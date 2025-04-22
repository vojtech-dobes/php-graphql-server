<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\Builtin;

use Vojtechdobes\GraphQL;


/**
 * @implements GraphQL\ScalarImplementation<string, string>
 */
final class StringScalarImplementation implements GraphQL\ScalarImplementation
{

	public function parseLiteralValue(GraphQL\Values\Value $value): string
	{
		if (!$value instanceof GraphQL\Values\StringValue) {
			throw new GraphQL\Exceptions\CannotParseScalarLiteralValueException();
		}

		return $value->value;
	}



	public function parseRuntimeValue(mixed $value): mixed
	{
		if (is_string($value) === false) {
			throw new GraphQL\Exceptions\CannotParseScalarRuntimeValueException();
		}

		return $value;
	}



	public function serialize(mixed $value): mixed
	{
		return $value;
	}

}
