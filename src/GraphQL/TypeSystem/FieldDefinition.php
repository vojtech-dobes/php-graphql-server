<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\TypeSystem;

use Vojtechdobes\GraphQL;


final class FieldDefinition
{

	/**
	 * @param list<GraphQL\Executable\Directive<GraphQL\Values\Value<mixed>>> $directives
	 * @param list<InputValueDefinition> $argumentDefinitions
	 */
	public function __construct(
		public readonly string $name,
		public readonly ?string $description,
		public readonly array $directives,
		public readonly array $argumentDefinitions,
		public readonly GraphQL\Types\Type $type,
	) {}

}
