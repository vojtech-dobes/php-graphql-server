<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\Executable;

use Vojtechdobes\GraphQL;


/**
 * @template-covariant TArgumentValue of GraphQL\Values\Value<mixed>|Variable
 */
final class Directive
{

	/**
	 * @param list<Argument<TArgumentValue>> $arguments
	 */
	public function __construct(
		public readonly string $name,
		public readonly array $arguments,
	) {}

}
