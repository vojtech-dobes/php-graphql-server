<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\Introspection;

use Vojtechdobes\GraphQL;


/**
 * @implements GraphQL\FieldResolver<
 *   null,
 *   GraphQL\Types\NamedType|null,
 *   array{
 *     name: string,
 *   },
 * >
 */
final class TypeFieldResolver implements GraphQL\FieldResolver
{

	public function __construct(
		private readonly GraphQL\TypeSystem\Schema $schema,
	) {}



	public function resolveField(mixed $objectValue, GraphQL\FieldSelection $field): mixed
	{
		$typeName = $field->arguments['name'];


		if ($this->schema->getTypeDefinitionOrNull($typeName) === null) {
			return null;
		}

		return new GraphQL\Types\NamedType($typeName);
	}

}
