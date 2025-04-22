<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\TypeSystem;

use Vojtechdobes\GraphQL;


final class InputValueDefinition
{

	/**
	 * @param GraphQL\Values\Value<mixed>|null $defaultValue
	 * @param list<GraphQL\Executable\Directive<GraphQL\Values\Value<mixed>>> $directives
	 */
	public function __construct(
		public readonly string $name,
		public readonly ?string $description,
		public readonly array $directives,
		public readonly GraphQL\Types\Type $type,
		public readonly ?GraphQL\Values\Value $defaultValue,
	) {}

}
