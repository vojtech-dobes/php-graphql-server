<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\TypeSystem\Builder;

use Vojtechdobes\GraphQL;


final class DirectivesUsageOnExtension
{

	/**
	 * @param list<GraphQL\Executable\Directive<GraphQL\Values\Value<mixed>>> $directives
	 * @param list<GraphQL\Executable\Directive<GraphQL\Values\Value<mixed>>> $baseDirectives
	 */
	public function __construct(
		public readonly string $location,
		public readonly GraphQL\Spec\TypeSystemDirectiveLocation $allowedDirectiveLocation,
		public readonly array $directives,
		public readonly array $baseDirectives,
	) {}

}
