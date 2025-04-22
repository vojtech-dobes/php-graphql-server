<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\Introspection;

use Vojtechdobes\GraphQL;


/**
 * @implements GraphQL\FieldResolver<
 *   GraphQL\Types\Type,
 *   list<GraphQL\TypeSystem\FieldDefinition>|null,
 *   array{
 *     includeDeprecated: bool,
 *   },
 * >
 */
final class TypeFieldsFieldResolver implements GraphQL\FieldResolver
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

		if (
			!$typeDefinition instanceof GraphQL\TypeSystem\InterfaceTypeDefinition
			&& !$typeDefinition instanceof GraphQL\TypeSystem\ObjectTypeDefinition
		) {
			return null;
		}

		$result = $typeDefinition->fields;

		if ($field->arguments['includeDeprecated'] === false) {
			$result = array_values(
				array_filter(
					$result,
					function (GraphQL\TypeSystem\FieldDefinition $fieldDefinition): bool {
						foreach ($fieldDefinition->directives as $directive) {
							if ($directive->name === 'deprecated') {
								return false;
							}
						}

						return true;
					},
				),
			);
		}

		return $result;
	}

}
