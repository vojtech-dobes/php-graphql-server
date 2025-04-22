<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\Executable;

use Vojtechdobes\GraphQL;


/**
 * @template-covariant TValue of GraphQL\Values\Value<mixed>|Variable
 */
final class Argument
{

	/**
	 * @param TValue $value
	 */
	public function __construct(
		public readonly string $name,
		public readonly GraphQL\Values\Value|Variable $value,
	) {}

}
