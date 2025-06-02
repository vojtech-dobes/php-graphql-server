<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL;


interface FieldResolverProvider
{

	function hasFieldResolver(string $fieldName): bool;



	/**
	 * This method is guaranteed to be called only for fields verified to be
	 * defined in the Schema.
	 *
	 * @return FieldResolver<mixed, mixed, covariant array<string, mixed>, mixed>|null
	 */
	function getFieldResolver(string $fieldName): ?FieldResolver;



	/**
	 * @return class-string<FieldResolver<mixed, mixed, covariant array<string, mixed>>>|null
	 */
	function getFieldResolverClass(string $fieldName): ?string;



	/**
	 * This method is only called in validation context. Therefore an optimized
	 * implementation doesn't have to support it if ExecutableSchema::validate()
	 * won't be called with it.
	 *
	 * @return list<string>
	 */
	function listSupportedFieldNames(): array;

}
