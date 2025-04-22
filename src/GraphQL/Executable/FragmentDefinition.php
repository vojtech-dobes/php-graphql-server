<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\Executable;

use Vojtechdobes\GraphQL;


final class FragmentDefinition
{

	/**
	 * @param list<Directive<GraphQL\Values\Value<mixed>>> $directives
	 * @param list<Selection> $selectionSet
	 */
	public function __construct(
		public readonly string $name,
		public readonly GraphQL\Types\NamedType $onType,
		public readonly array $directives,
		public readonly array $selectionSet,
	) {}

}
