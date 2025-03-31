<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\Introspection;

use Vojtechdobes\GraphQL;


/**
 * @implements GraphQL\FieldResolver<
 *   GraphQL\Types\Type,
 *   string|null,
 * >
 */
final class TypeSpecifiedByUrlFieldResolver implements GraphQL\FieldResolver
{

	public function __construct(
		private readonly GraphQL\TypeSystem\Schema $schema,
	) {}



	public function resolveField(mixed $objectValue, GraphQL\FieldSelection $field): mixed
	{
		if (!$objectValue instanceof GraphQL\Types\NamedType) {
			return null;
		}

		/** @var GraphQL\TypeSystem\TypeDefinition $typeDefinition */
		$typeDefinition = $this->schema->getTypeDefinition($objectValue->name);

		foreach ($typeDefinition->directives as $directive) {
			if ($directive->name === 'specifiedBy') {
				foreach ($directive->arguments as $directiveArgument) {
					if ($directiveArgument->name === 'url') {
						return $directiveArgument->value->getRuntimeValue();
					}
				}
			}
		}

		return null;
	}

}
