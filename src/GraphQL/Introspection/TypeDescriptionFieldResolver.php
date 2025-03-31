<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\Introspection;

use Vojtechdobes\GraphQL;


/**
 * @implements GraphQL\FieldResolver<
 *   GraphQL\Types\Type,
 *   string|null,
 * >
 */
final class TypeDescriptionFieldResolver implements GraphQL\FieldResolver
{

	public function __construct(
		private readonly GraphQL\TypeSystem\Schema $schema,
	) {}



	public function resolveField(mixed $objectValue, GraphQL\FieldSelection $field): mixed
	{
		if (!$objectValue instanceof GraphQL\Types\NamedType) {
			return null;
		}

		return $this->schema->getTypeDefinition($objectValue->name)->description;
	}

}
