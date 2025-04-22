<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL;


/**
 * @phpstan-type PHPStan_ExecutableDefinition Executable\FragmentDefinition|Executable\OperationDefinition
 * @phpstan-type PHPStan_TypeSystemDefinition TypeSystem\Builder\RootOperationTypes|TypeSystem\DirectiveDefinition|TypeSystem\EnumTypeDefinition|TypeSystem\InputObjectTypeDefinition|TypeSystem\InterfaceTypeDefinition|TypeSystem\ObjectTypeDefinition|TypeSystem\ScalarTypeDefinition|TypeSystem\UnionTypeDefinition
 * @phpstan-type PHPStan_TypeSystemExtensionDocument TypeSystem\Builder\RootOperationTypes|TypeSystem\Builder\SchemaExtension|TypeSystem\DirectiveDefinition|TypeSystem\EnumTypeDefinition|TypeSystem\InputObjectTypeDefinition|TypeSystem\InterfaceTypeDefinition|TypeSystem\ObjectTypeDefinition|TypeSystem\ScalarTypeDefinition|TypeSystem\UnionTypeDefinition|TypeSystem\TypeExtension
 */
final class Spec
{

	public const string DefaultMutationType = 'Mutation';
	public const string DefaultQueryType = 'Query';
	public const string DefaultSubscriptionType = 'Subscription';



	/**
	 * Only input types are relevant in context of this algorithm.
	 *
	 * @see https://spec.graphql.org/October2021/#AreTypesCompatible()
	 */
	public static function areTypesCompatible(
		Types\Type $variableType,
		Types\Type $locationType,
	): bool
	{
		return match (true) {
			$locationType instanceof Types\NonNullType => (
				$variableType instanceof Types\NonNullType
				&& self::areTypesCompatible(
					variableType: $variableType->getWrappedType(),
					locationType: $locationType->getWrappedType(),
				)
			),
			$variableType instanceof Types\NonNullType => self::areTypesCompatible(
				variableType: $variableType->getWrappedType(),
				locationType: $locationType,
			),
			$locationType instanceof Types\ListType => (
				$variableType instanceof Types\ListType
				&& self::areTypesCompatible(
					variableType: $variableType->getWrappedType(),
					locationType: $locationType->getWrappedType(),
				)
			),
			$variableType instanceof Types\ListType => false,
			default => $variableType->getNamedType() === $locationType->getNamedType(),
		};
	}



	/**
	 * @param TypeSystem\Registry<TypeSystem\TypeDefinition> $typeDefinitionRegistry
	 *
	 * @see https://spec.graphql.org/October2021/#IsValidImplementationFieldType()
	 */
	public static function isValidImplementationFieldType(
		TypeSystem\Registry $typeDefinitionRegistry,
		Types\Type $fieldType,
		Types\Type $implementedFieldType,
	): bool
	{
		if ($fieldType instanceof Types\NonNullType) {
			return self::isValidImplementationFieldType(
				$typeDefinitionRegistry,
				$fieldType->getWrappedType(),
				$implementedFieldType instanceof Types\NonNullType
					? $implementedFieldType->getWrappedType()
					: $implementedFieldType,
			);
		}

		if ($fieldType instanceof Types\ListType) {
			if ($implementedFieldType instanceof Types\ListType) {
				return self::isValidImplementationFieldType(
					$typeDefinitionRegistry,
					$fieldType->getWrappedType(),
					$implementedFieldType->getWrappedType(),
				);
			}

			return false;
		}

		if ($implementedFieldType instanceof Types\NamedType) {
			if ($fieldType->name === $implementedFieldType->name) {
				return true;
			}

			/** @var TypeSystem\TypeDefinition $fieldTypeDefinition */
			$fieldTypeDefinition = $typeDefinitionRegistry->getItem($fieldType->name);
			/** @var TypeSystem\TypeDefinition $implementedFieldTypeDefinition */
			$implementedFieldTypeDefinition = $typeDefinitionRegistry->getItem($implementedFieldType->name);

			if (
				$fieldTypeDefinition instanceof TypeSystem\ObjectTypeDefinition
				&& $implementedFieldTypeDefinition instanceof TypeSystem\UnionTypeDefinition
			) {
				return in_array($fieldType->name, $implementedFieldTypeDefinition->possibleTypes, true);
			}

			if (
				(
					$fieldTypeDefinition instanceof TypeSystem\InterfaceTypeDefinition
					|| $fieldTypeDefinition instanceof TypeSystem\ObjectTypeDefinition
				)
				&& $implementedFieldTypeDefinition instanceof TypeSystem\InterfaceTypeDefinition
			) {
				return in_array($implementedFieldType->name, $fieldTypeDefinition->implementedInterfaces, true);
			}
		}

		return false;
	}

}
