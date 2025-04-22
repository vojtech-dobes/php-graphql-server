<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\Introspection;

use Vojtechdobes\GraphQL;


/**
 * @implements GraphQL\FieldResolver<
 *   GraphQL\Types\Type,
 *   list<GraphQL\TypeSystem\InputValueDefinition>|null,
 * >
 */
final class TypeInputFieldsFieldResolver implements GraphQL\FieldResolver
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

		if (!$typeDefinition instanceof GraphQL\TypeSystem\InputObjectTypeDefinition) {
			return null;
		}

		return $typeDefinition->fields;
	}

}
