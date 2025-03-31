<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\Executable;

use Vojtechdobes\GraphQL;


final class Field implements Selection
{

	/**
	 * @param list<Argument<GraphQL\Values\Value<mixed>|Variable>> $arguments
	 * @param list<Directive<GraphQL\Values\Value<mixed>|Variable>> $directives
	 * @param list<Selection>|null $selectionSet
	 */
	public function __construct(
		public readonly ?string $alias,
		public readonly string $name,
		public readonly array $arguments,
		public readonly array $directives,
		public readonly ?array $selectionSet,
	) {}



	public function getResponseKey(): string
	{
		return $this->alias ?? $this->name;
	}

}
