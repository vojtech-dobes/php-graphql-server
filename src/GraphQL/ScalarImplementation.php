<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL;


/**
 * @template TValue
 * @template TSerializedValue
 */
interface ScalarImplementation
{

	/**
	 * @param Values\Value<mixed> $value
	 * @return TValue
	 * @throws Exceptions\CannotParseScalarLiteralValueException
	 */
	function parseLiteralValue(Values\Value $value): mixed;



	/**
	 * @return TValue
	 * @throws Exceptions\CannotParseScalarRuntimeValueException
	 */
	function parseRuntimeValue(mixed $value): mixed;



	/**
	 * @return TSerializedValue
	 * @throws Exceptions\CannotSerializeScalarValueException
	 */
	function serialize(mixed $value): mixed;

}
