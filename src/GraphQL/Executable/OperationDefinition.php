<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\Executable;

use Vojtechdobes\GraphQL;


final class OperationDefinition
{

	/**
	 * @param list<VariableDefinition> $variableDefinitions
	 * @param list<Directive<GraphQL\Values\Value<mixed>>> $directives
	 * @param list<Selection> $selectionSet
	 */
	public function __construct(
		public readonly GraphQL\OperationType $type,
		public readonly ?string $name,
		public readonly array $variableDefinitions,
		public readonly array $directives,
		public readonly array $selectionSet,
	) {}



	public function format(): string
	{
		return $this->name !== null
			? "{$this->type->format()} '{$this->name}'"
			: $this->type->format();
	}

}
