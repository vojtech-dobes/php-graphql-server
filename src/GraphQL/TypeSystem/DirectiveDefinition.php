<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\TypeSystem;

use Vojtechdobes\GraphQL;


final class DirectiveDefinition
{

	/**
	 * @param list<InputValueDefinition> $argumentDefinitions
	 * @param list<GraphQL\Spec\ExecutableDirectiveLocation|GraphQL\Spec\TypeSystemDirectiveLocation> $locations
	 */
	public function __construct(
		public readonly array $argumentDefinitions,
		public readonly ?string $description,
		public readonly bool $isRepeatable,
		public readonly array $locations,
		public readonly string $name,
	) {}

}
