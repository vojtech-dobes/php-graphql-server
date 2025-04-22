<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\Introspection;

use Vojtechdobes\GraphQL;


/**
 * @implements GraphQL\FieldResolver<
 *   GraphQL\Types\Type,
 *   GraphQL\TypeSystem\TypeKind,
 * >
 */
final class TypeKindFieldResolver implements GraphQL\FieldResolver
{

	public function __construct(
		private readonly GraphQL\TypeSystem\Schema $schema,
	) {}



	public function resolveField(mixed $objectValue, GraphQL\FieldSelection $field): mixed
	{
		return match (true) {
			$objectValue instanceof GraphQL\Types\ListType => GraphQL\TypeSystem\TypeKind::List_,
			$objectValue instanceof GraphQL\Types\NamedType => $this->schema->getTypeDefinition($objectValue->name)->kind,
			$objectValue instanceof GraphQL\Types\NonNullType => GraphQL\TypeSystem\TypeKind::NonNull,
		};
	}

}
