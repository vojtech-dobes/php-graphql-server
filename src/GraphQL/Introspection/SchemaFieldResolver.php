<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\Introspection;

use Vojtechdobes\GraphQL;


/**
 * @implements GraphQL\FieldResolver<
 *   null,
 *   GraphQL\TypeSystem\Schema,
 * >
 */
final class SchemaFieldResolver implements GraphQL\FieldResolver
{

	public function __construct(
		private readonly GraphQL\TypeSystem\Schema $schema,
	) {}



	public function resolveField(mixed $objectValue, GraphQL\FieldSelection $field): mixed
	{
		return $this->schema;
	}

}
