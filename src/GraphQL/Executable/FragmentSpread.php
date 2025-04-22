<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\Executable;

use Vojtechdobes\GraphQL;


final class FragmentSpread implements Selection
{

	/**
	 * @param list<Directive<GraphQL\Values\Value<mixed>|Variable>> $directives
	 */
	public function __construct(
		public readonly string $fragmentName,
		public readonly array $directives,
	) {}

}
