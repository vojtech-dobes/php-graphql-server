<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL;


final class StaticAbstractTypeResolverProvider implements AbstractTypeResolverProvider
{

	/**
	 * @param array<string, AbstractTypeResolver<mixed>> $abstractTypeResolvers
	 */
	public function __construct(
		private readonly array $abstractTypeResolvers,
	) {}



	public function hasAbstractTypeResolver(string $abstractTypeName): bool
	{
		return array_key_exists($abstractTypeName, $this->abstractTypeResolvers);
	}



	public function getAbstractTypeResolver(string $abstractTypeName): AbstractTypeResolver
	{
		return $this->abstractTypeResolvers[$abstractTypeName];
	}



	public function listSupportedTypeNames(): array
	{
		return array_keys($this->abstractTypeResolvers);
	}

}
