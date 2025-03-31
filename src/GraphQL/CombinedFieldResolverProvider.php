<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL;


final class CombinedFieldResolverProvider implements FieldResolverProvider
{

	/**
	 * @param list<FieldResolverProvider> $fieldResolverProviders
	 */
	public function __construct(
		private readonly array $fieldResolverProviders,
	) {}



	public function hasFieldResolver(string $fieldName): bool
	{
		return array_any(
			$this->fieldResolverProviders,
			static fn ($fieldResolverProvider) => $fieldResolverProvider->hasFieldResolver($fieldName),
		);
	}



	public function getFieldResolver(string $fieldName): ?FieldResolver
	{
		foreach ($this->fieldResolverProviders as $fieldResolverProvider) {
			$fieldResolver = $fieldResolverProvider->getFieldResolver($fieldName);

			if ($fieldResolver !== null) {
				return $fieldResolver;
			}
		}

		return null;
	}



	public function listSupportedFieldNames(): array
	{
		return array_merge(
			...array_map(
				static fn ($fieldResolverProvider) => $fieldResolverProvider->listSupportedFieldNames(),
				$this->fieldResolverProviders,
			),
		);
	}

}
