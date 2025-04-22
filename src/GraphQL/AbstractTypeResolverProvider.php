<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL;


interface AbstractTypeResolverProvider
{

	function hasAbstractTypeResolver(string $abstractTypeName): bool;



	/**
	 * This method is guaranteed to be called only for abstract types verified
	 * to be defined in the Schema.
	 *
	 * @return AbstractTypeResolver<mixed>
	 */
	function getAbstractTypeResolver(string $abstractTypeName): AbstractTypeResolver;



	/**
	 * This method is only called in validation context. Therefore an optimized
	 * implementation doesn't have to support it if ExecutableSchema::validate()
	 * won't be called with it.
	 *
	 * @return list<string>
	 */
	function listSupportedTypeNames(): array;

}
