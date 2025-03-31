<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\Values;


/**
 * @template TValue
 */
interface Value
{

	/**
	 * @return TValue
	 */
	function getRuntimeValue(): mixed;



	function getLabel(): string;



	function toString(): string;

}
