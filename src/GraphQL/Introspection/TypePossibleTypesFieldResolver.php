<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\Introspection;

use Vojtechdobes\GraphQL;


/**
 * @implements GraphQL\FieldResolver<
 *   GraphQL\Types\Type,
 *   list<GraphQL\Types\NamedType>|null,
 * >
 */
final class TypePossibleTypesFieldResolver implements GraphQL\FieldResolver
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

		if ($typeDefinition instanceof GraphQL\TypeSystem\InterfaceTypeDefinition) {
			$result = [];

			foreach ($this->schema->getTypeDefinitions() as $implementingTypeDefinition) {
				if (
					(
						$implementingTypeDefinition instanceof GraphQL\TypeSystem\InterfaceTypeDefinition
						|| $implementingTypeDefinition instanceof GraphQL\TypeSystem\ObjectTypeDefinition
					) && in_array(
						$typeDefinition->name,
						$implementingTypeDefinition->implementedInterfaces,
						true,
					)
				) {
					$result[] = new GraphQL\Types\NamedType($implementingTypeDefinition->name);
				}
			}

			return $result;
		} elseif ($typeDefinition instanceof GraphQL\TypeSystem\UnionTypeDefinition) {
			return array_map(
				fn ($possibleType) => new GraphQL\Types\NamedType($possibleType),
				$typeDefinition->possibleTypes,
			);
		}

		return null;
	}

}
