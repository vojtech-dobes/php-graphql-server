<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\TypeSystem;

use BackedEnum;
use Vojtechdobes\GraphQL;


final class Schema
{

	/**
	 * @param Registry<DirectiveDefinition> $directiveDefinitionRegistry
	 * @param array<string, class-string<BackedEnum>> $enumClasses
	 * @param array{
	 *   mutation: string|null,
	 *   query: string,
	 *   subscription: string|null,
	 * } $rootOperationTypes
	 * @param Registry<GraphQL\ScalarImplementation<mixed, mixed>> $scalarImplementationRegistry
	 * @param Registry<TypeDefinition> $typeDefinitionRegistry
	 */
	public function __construct(
		public readonly ?string $description,
		public readonly Registry $directiveDefinitionRegistry,
		public readonly array $enumClasses,
		public readonly array $rootOperationTypes,
		public readonly Registry $scalarImplementationRegistry,
		public readonly Registry $typeDefinitionRegistry,
	) {}



	public function getDirectiveDefinition(string $directiveName): DirectiveDefinition
	{
		return $this->directiveDefinitionRegistry->getItem($directiveName);
	}



	public function getDirectiveDefinitionOrNull(string $directiveName): ?DirectiveDefinition
	{
		return $this->directiveDefinitionRegistry->getItemOrNull($directiveName);
	}



	/**
	 * @return array<string, DirectiveDefinition>
	 */
	public function getDirectiveDefinitions(): array
	{
		return $this->directiveDefinitionRegistry->getAll();
	}



	/**
	 * @return class-string<BackedEnum>|null
	 */
	public function getEnumClass(string $enumName): ?string
	{
		return $this->enumClasses[$enumName] ?? null;
	}



	public function getRootOperationTypeDefinition(GraphQL\OperationType $operationType): ?ObjectTypeDefinition
	{
		$typeName = $this->rootOperationTypes[$operationType->value];

		if ($typeName === null) {
			return null;
		}

		return $this->typeDefinitionRegistry->getItem($typeName);
	}



	public function getTypeDefinition(string $typeName): TypeDefinition
	{
		return $this->typeDefinitionRegistry->getItem($typeName);
	}



	public function getTypeDefinitionOrNull(string $typeName): ?TypeDefinition
	{
		return $this->typeDefinitionRegistry->getItemOrNull($typeName);
	}



	/**
	 * @return array<string, TypeDefinition>
	 */
	public function getTypeDefinitions(): array
	{
		return $this->typeDefinitionRegistry->getAll();
	}

}
