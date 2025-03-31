<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\TypeSystem\Builder;

use Vojtechdobes\GraphQL;


final class DirectivesUsage
{

	/**
	 * @param list<GraphQL\Executable\Directive<GraphQL\Values\Value<mixed>>> $directives
	 */
	public function __construct(
		public readonly string $location,
		public readonly GraphQL\Spec\TypeSystemDirectiveLocation $allowedDirectiveLocation,
		public readonly array $directives,
	) {}

}
