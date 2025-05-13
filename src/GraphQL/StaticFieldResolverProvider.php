<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL;


final class StaticFieldResolverProvider implements FieldResolverProvider
{

	/**
	 * @param array<string, FieldResolver<mixed, mixed, covariant array<string, mixed>>> $fieldResolvers
	 */
	public function __construct(
		private readonly array $fieldResolvers,
	) {}



	public function hasFieldResolver(string $fieldName): bool
	{
		return array_key_exists($fieldName, $this->fieldResolvers);
	}



	public function getFieldResolver(string $fieldName): ?FieldResolver
	{
		return $this->fieldResolvers[$fieldName] ?? null;
	}



	public function listSupportedFieldNames(): array
	{
		return array_keys($this->fieldResolvers);
	}

}
