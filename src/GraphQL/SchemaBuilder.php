<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL;

use BackedEnum;
use LogicException;
use UnitEnum;


final class SchemaBuilder
{

	/** @var array<string, TypeSystem\DirectiveDefinition> */
	private array $directiveDefinitions = [];

	/** @var array<string, TypeSystem\DirectiveDefinition> */
	private array $directiveDefinitionsAvailable;

	/** @var list<TypeSystem\Builder\DirectivesUsage> */
	private array $directivesUsage = [];

	/** @var list<TypeSystem\Builder\DirectivesUsageOnExtension> */
	private array $directivesUsageOnExtension = [];

	/** @var array<string, class-string<BackedEnum>> */
	private array $enumClasses = [];

	/** @var array<string, class-string<BackedEnum>> */
	private array $enumClassesAvailable;

	private readonly Errors $errors;
	private ?self $extension = null;

	private ?TypeSystem\Builder\RootOperationTypes $rootOperationTypes = null;
	private ?TypeSystem\Builder\RootOperationTypes $rootOperationTypesBase = null;
	private ?TypeSystem\Builder\RootOperationTypes $rootOperationTypesExtension = null;

	/** @var array<string, ScalarImplementation<mixed, mixed>> */
	private array $scalarImplementations = [];

	/** @var array<string, ScalarImplementation<mixed, mixed>> */
	private array $scalarImplementationsAvailable;

	/** @var array<string, TypeSystem\TypeDefinition> */
	private array $typeExtensions = [];

	/** @var list<TypeSystem\EnumTypeDefinition> */
	private array $typeExtensionsEnum = [];

	/** @var list<TypeSystem\InputObjectTypeDefinition> */
	private array $typeExtensionsInput = [];

	/** @var list<TypeSystem\InterfaceTypeDefinition> */
	private array $typeExtensionsInterface = [];

	/** @var list<TypeSystem\ObjectTypeDefinition> */
	private array $typeExtensionsObject = [];

	/** @var list<TypeSystem\ScalarTypeDefinition> */
	private array $typeExtensionsScalar = [];

	/** @var list<TypeSystem\UnionTypeDefinition> */
	private array $typeExtensionsUnion = [];

	/** @var array<string, TypeSystem\TypeDefinition> */
	private array $types = [];

	/** @var array<string, TypeSystem\TypeDefinition> */
	private array $typesAvailable;

	/** @var list<TypeSystem\EnumTypeDefinition> */
	private array $typesEnum = [];

	/** @var list<TypeSystem\InputObjectTypeDefinition> */
	private array $typesInput = [];

	/** @var list<TypeSystem\InterfaceTypeDefinition> */
	private array $typesInterface = [];

	/** @var list<TypeSystem\ObjectTypeDefinition> */
	private array $typesObject = [];

	/** @var list<TypeSystem\ScalarTypeDefinition> */
	private array $typesScalar = [];

	/** @var list<TypeSystem\UnionTypeDefinition> */
	private array $typesUnion = [];

	private Validator $validator;



	public function __construct(
		private readonly bool $allowReservedNames,
	)
	{
		$this->errors = new Errors();
	}



	public function setRootOperationTypes(TypeSystem\Builder\RootOperationTypes $rootOperationTypes): void
	{
		if ($this->rootOperationTypes !== null) {
			$this->errors->addErrorMessage(
				"Schema definition can't be present multiple times",
			);

			return;
		}

		$this->rootOperationTypes = $rootOperationTypes;
	}



	public function addDirectiveDefinition(TypeSystem\DirectiveDefinition $directiveDefinition): void
	{
		if (array_key_exists($directiveDefinition->name, $this->directiveDefinitions)) {
			$this->errors->addErrorMessage(
				sprintf(
					"Directive '%s' can't be defined multiple times",
					$directiveDefinition->name,
				),
			);

			return;
		}

		$this->directiveDefinitions[$directiveDefinition->name] = $directiveDefinition;
	}



	/**
	 * @param class-string<UnitEnum> $enumClass
	 */
	public function addEnumClass(
		string $enumName,
		string $enumClass,
	): void
	{
		if (array_key_exists($enumName, $this->enumClasses)) {
			throw new LogicException(
				"There can be only one enum class for enum type '{$enumName}'",
			);
		}

		if (is_a($enumClass, BackedEnum::class, true) === false) {
			$this->errors->addErrorMessage(
				"Enum class for enum type '{$enumName}' must be a " . BackedEnum::class,
			);

			return;
		}

		$this->enumClasses[$enumName] = $enumClass;
	}



	/**
	 * @param ScalarImplementation<mixed, mixed> $scalarImplementation
	 */
	public function addScalarImplementation(
		string $scalarName,
		ScalarImplementation $scalarImplementation,
	): void
	{
		if (array_key_exists($scalarName, $this->scalarImplementations)) {
			throw new LogicException(
				"There can be only one implementation for scalar type '{$scalarName}'",
			);
		}

		$this->scalarImplementations[$scalarName] = $scalarImplementation;
	}



	public function addSchemaExtension(TypeSystem\Builder\SchemaExtension $schemaExtension): void
	{
		if ($this->rootOperationTypesExtension !== null) {
			$this->errors->addErrorMessage(
				"Schema definition can't be extended multiple times",
			);

			return;
		}

		$this->rootOperationTypesExtension = $schemaExtension->rootOperationTypes;
	}



	public function addTypeDefinition(TypeSystem\TypeDefinition $typeDefinition): void
	{
		if (array_key_exists($typeDefinition->name, $this->types)) {
			$this->errors->addErrorMessage(
				sprintf(
					"Type '%s' can't be defined multiple times",
					$typeDefinition->name,
				),
			);

			return;
		}

		$this->types[$typeDefinition->name] = $typeDefinition;

		match (true) {
			$typeDefinition instanceof TypeSystem\EnumTypeDefinition => $this->typesEnum[] = $typeDefinition,
			$typeDefinition instanceof TypeSystem\InputObjectTypeDefinition => $this->typesInput[] = $typeDefinition,
			$typeDefinition instanceof TypeSystem\InterfaceTypeDefinition => $this->typesInterface[] = $typeDefinition,
			$typeDefinition instanceof TypeSystem\ObjectTypeDefinition => $this->typesObject[] = $typeDefinition,
			$typeDefinition instanceof TypeSystem\ScalarTypeDefinition => $this->typesScalar[] = $typeDefinition,
			$typeDefinition instanceof TypeSystem\UnionTypeDefinition => $this->typesUnion[] = $typeDefinition,
		};
	}



	public function addTypeExtension(TypeSystem\TypeExtension $typeExtension): void
	{
		$typeDefinition = $typeExtension->typeDefinition;

		if (array_key_exists($typeDefinition->name, $this->typeExtensions)) {
			$this->errors->addErrorMessage(
				sprintf(
					"Type '%s' can't be extended multiple times",
					$typeDefinition->name,
				),
			);

			return;
		}

		$this->typeExtensions[$typeDefinition->name] = $typeDefinition;
	}



	/**
	 * @return $this
	 */
	public function extendWith(self $schemaBuilder): self
	{
		if ($this->extension === null) {
			$this->extension = $schemaBuilder;
		} else {
			$this->extension->extendWith($schemaBuilder);
		}

		return $this;
	}



	/**
	 * @throws Exceptions\InconsistentSchemaException
	 */
	public function buildSchema(): TypeSystem\Schema
	{
		$this->directiveDefinitionsAvailable = $this->directiveDefinitions;
		$this->enumClassesAvailable = $this->enumClasses;
		$this->scalarImplementationsAvailable = $this->scalarImplementations;
		$this->rootOperationTypesBase = $this->rootOperationTypes;
		$this->typesAvailable = $this->types;

		$this->validate();

		if ($this->errors->errors !== []) {
			throw new Exceptions\InconsistentSchemaException($this->errors->errors);
		}

		$schemaBlocks = $this->createSchemaBlocks();

		$schemaBlocks['rootOperationTypes'] ??= new TypeSystem\Builder\RootOperationTypes(
			description: null,
			directives: [],
			types: [],
		);

		$rootOperationTypes = [
			'mutation' => null,
			'query' => null,
			'subscription' => null,
		];

		foreach ($schemaBlocks['rootOperationTypes']->types as $rootOperationType) {
			$rootOperationTypes[$rootOperationType['operationType']->value] = $rootOperationType['type'];
		}

		$queryRootOperationType = $rootOperationTypes['query'] ?? Spec::DefaultQueryType;

		if ($rootOperationTypes['query'] === null) {
			if (($schemaBlocks['types'][Spec::DefaultQueryType] ?? null) instanceof TypeSystem\ObjectTypeDefinition) {
				$rootOperationTypes['query'] = Spec::DefaultQueryType;
			} else {
				throw new Exceptions\InconsistentSchemaException([
					new Error(
						"Schema must define query root operation type '{$queryRootOperationType}'",
					),
				]);
			}
		}

		if (
			$rootOperationTypes['mutation'] === null
			&& ($schemaBlocks['types'][Spec::DefaultMutationType] ?? null) instanceof TypeSystem\ObjectTypeDefinition
		) {
			$rootOperationTypes['mutation'] = Spec::DefaultMutationType;
		}

		if (
			$rootOperationTypes['subscription'] === null
			&& ($schemaBlocks['types'][Spec::DefaultSubscriptionType] ?? null) instanceof TypeSystem\ObjectTypeDefinition
		) {
			$rootOperationTypes['subscription'] = Spec::DefaultSubscriptionType;
		}

		return new TypeSystem\Schema(
			description: $schemaBlocks['rootOperationTypes']->description,
			directiveDefinitionRegistry: new TypeSystem\StaticRegistry($schemaBlocks['directiveDefinitions']),
			enumClasses: $schemaBlocks['enumClasses'],
			rootOperationTypes: $rootOperationTypes,
			scalarImplementationRegistry: new TypeSystem\StaticRegistry($schemaBlocks['scalarImplementations']),
			typeDefinitionRegistry: new TypeSystem\StaticRegistry($schemaBlocks['types']),
		);
	}



	private function validate(): void
	{
		foreach ($this->typesScalar as $scalarTypeDefinition) {
			if (array_key_exists($scalarTypeDefinition->name, $this->scalarImplementationsAvailable) === false) {
				$this->errors->addErrorMessage(
					"Scalar type '{$scalarTypeDefinition->name}' doesn't have an implementation",
				);
			}
		}

		if ($this->errors->errors !== []) {
			return;
		}

		$this->validator = new Validator(
			directiveDefinitionRegistry: new TypeSystem\StaticRegistry($this->directiveDefinitionsAvailable),
			scalarImplementationRegistry: new TypeSystem\StaticRegistry($this->scalarImplementationsAvailable),
			typeDefinitionRegistry: new TypeSystem\StaticRegistry($this->typesAvailable),
		);

		$this->validateRootOperationTypesExtension();
		$this->validateGeneralTypeExtensionCorrectness();
		$this->validateAndApplyEnumTypeExtensions();
		$this->validateAndApplyScalarTypeExtensions();
		$this->validateAndApplyUnionTypeExtensions();

		$this->validator = new Validator(
			directiveDefinitionRegistry: new TypeSystem\StaticRegistry($this->directiveDefinitionsAvailable),
			scalarImplementationRegistry: new TypeSystem\StaticRegistry($this->scalarImplementationsAvailable),
			typeDefinitionRegistry: new TypeSystem\StaticRegistry($this->typesAvailable),
		);

		$this->validateAndApplyInputObjectTypeExtensions();
		$this->validateAndApplyInterfaceTypeExtensions();
		$this->validateAndApplyObjectTypeExtensions();

		$this->validateDirectiveDefinitions();
		$this->validateEnumTypes();
		$this->validateInputObjectTypes();
		$this->validateInterfaceTypes();
		$this->validateObjectTypes();
		$this->validateScalarTypes();
		$this->validateUnionTypes();
		$this->validateRootOperationTypes();
		$this->validateDirectiveUsage();

		if ($this->extension !== null) {
			if ($this->rootOperationTypes !== null) {
				$this->extension->rootOperationTypesBase = $this->rootOperationTypes;
			} elseif ($this->rootOperationTypesExtension !== null) {
				$this->extension->rootOperationTypesBase = new TypeSystem\Builder\RootOperationTypes(
					description: $this->rootOperationTypesBase->description,
					directives: array_merge(
						$this->rootOperationTypesBase->directives,
						$this->rootOperationTypesExtension->directives,
					),
					types: array_merge(
						$this->rootOperationTypesBase->types,
						$this->rootOperationTypesExtension->types,
					),
				);
			}

			$this->extension->directiveDefinitionsAvailable = array_replace(
				$this->extension->directiveDefinitions,
				$this->directiveDefinitionsAvailable,
			);

			$this->extension->enumClassesAvailable = array_replace(
				$this->extension->enumClasses,
				$this->enumClassesAvailable,
			);

			$this->extension->scalarImplementationsAvailable = array_replace(
				$this->extension->scalarImplementations,
				$this->scalarImplementationsAvailable,
			);

			$this->extension->typesAvailable = array_replace(
				$this->extension->types,
				$this->typesAvailable,
			);

			$this->extension->validate();

			foreach ($this->extension->errors->errors as $error) {
				$this->errors->addError($error);
			}
		} else {
			foreach (array_keys($this->scalarImplementationsAvailable) as $scalarTypeName) {
				$typeDefinition = $this->typesAvailable[$scalarTypeName] ?? null;

				if (!$typeDefinition instanceof TypeSystem\ScalarTypeDefinition) {
					$this->errors->addErrorMessage(
						sprintf(
							"Scalar implementation can't be registered for %s type '%s'",
							$typeDefinition !== null
								? $typeDefinition->kind->format()
								: 'unknown',
							$scalarTypeName,
						),
					);
				}
			}

			foreach (array_keys($this->enumClassesAvailable) as $enumTypeName) {
				$typeDefinition = $this->typesAvailable[$enumTypeName] ?? null;

				if (!$typeDefinition instanceof TypeSystem\EnumTypeDefinition) {
					$this->errors->addErrorMessage(
						sprintf(
							"Enum class can't be registered for %s type '%s'",
							$typeDefinition !== null
								? $typeDefinition->kind->format()
								: 'unknown',
							$enumTypeName,
						),
					);
				}
			}
		}
	}



	/**
	 * @return array{
	 *   directiveDefinitions: array<string, TypeSystem\DirectiveDefinition>,
	 *   enumClasses: array<string, class-string<BackedEnum>>,
	 *   rootOperationTypes: TypeSystem\Builder\RootOperationTypes|null,
	 *   scalarImplementations: array<string, ScalarImplementation<mixed, mixed>>,
	 *   types: array<string, TypeSystem\TypeDefinition>,
	 * }
	 */
	private function createSchemaBlocks(): array
	{
		$directiveDefinitions = $this->directiveDefinitions;
		$enumClasses = $this->enumClasses;
		$rootOperationTypes = $this->rootOperationTypes;
		$scalarImplementations = $this->scalarImplementations;
		$types = $this->types;

		if ($this->extension !== null) {
			$extensionSchemaBlocks = $this->extension->createSchemaBlocks();

			$directiveDefinitions = array_replace($directiveDefinitions, $extensionSchemaBlocks['directiveDefinitions']);
			$enumClasses = array_replace($enumClasses, $extensionSchemaBlocks['enumClasses']);
			$scalarImplementations = array_replace($scalarImplementations, $extensionSchemaBlocks['scalarImplementations']);
			$types = array_replace($types, $extensionSchemaBlocks['types']);

			$rootOperationTypes = $extensionSchemaBlocks['rootOperationTypes'];
		}

		return [
			'directiveDefinitions' => $directiveDefinitions,
			'enumClasses' => $enumClasses,
			'rootOperationTypes' => $rootOperationTypes,
			'scalarImplementations' => $scalarImplementations,
			'types' => $types,
		];
	}



	/**
	 * @param list<TypeSystem\InputValueDefinition> $argumentDefinitions
	 */
	private function validateArgumentDefinitions(
		string $location,
		array $argumentDefinitions,
	): void
	{
		Validator::validateDuplicates(
			$this->errors,
			$location,
			'define argument',
			$argumentDefinitions,
			static fn ($argumentDefinition) => $argumentDefinition->name,
		);

		foreach ($argumentDefinitions as $argumentDefinition) {
			$argumentLocation = sprintf(
				"Argument '%s' of %s",
				$argumentDefinition->name,
				lcfirst($location),
			);

			$this->validateName(
				sprintf('Argument of %s', lcfirst($location)),
				$argumentDefinition->name,
			);

			$this->validator->validateInputType(
				$this->errors,
				$argumentLocation,
				$argumentDefinition->type,
			);

			if ($argumentDefinition->defaultValue !== null) {
				$this->validator->validateValue(
					$this->errors,
					sprintf('Default value of %s', lcfirst($argumentLocation)),
					$argumentDefinition->type,
					$argumentDefinition->defaultValue,
				);
			}

			$this->directivesUsage[] = new TypeSystem\Builder\DirectivesUsage(
				$argumentLocation,
				Spec\TypeSystemDirectiveLocation::ArgumentDefinition,
				$argumentDefinition->directives,
			);
		}
	}



	private function validateRootOperationTypesExtension(): void
	{
		if ($this->rootOperationTypesExtension === null) {
			return;
		}

		if ($this->rootOperationTypes !== null) {
			$this->errors->addErrorMessage(
				"Schema definition can't be defined and extended at the same time",
			);

			return;
		}

		if ($this->rootOperationTypesBase === null) {
			$this->errors->addErrorMessage(
				"Schema definition can't be extended because it's not defined",
			);

			return;
		}

		Validator::validateDuplicates(
			$this->errors,
			'Schema definition',
			'be extended to define root operation type',
			$this->rootOperationTypesExtension->types,
			static fn ($rootOperationType) => $rootOperationType['operationType']->value,
		);

		$this->directivesUsageOnExtension[] = new TypeSystem\Builder\DirectivesUsageOnExtension(
			'Schema definition',
			Spec\TypeSystemDirectiveLocation::Schema,
			$this->rootOperationTypesExtension->directives,
			$this->rootOperationTypesBase->directives,
		);

		foreach ($this->rootOperationTypesExtension->types as $rootOperationType) {
			$type = $rootOperationType['type'];
			$operationType = $rootOperationType['operationType'];

			$typeDefinition = $this->typesAvailable[$type] ?? null;

			if ($typeDefinition === null) {
				$this->errors->addErrorMessage(
					sprintf(
						"%s root operation has unknown type '%s'",
						$operationType->format(),
						$type,
					),
				);

				continue;
			}

			if ($typeDefinition->kind !== TypeSystem\TypeKind::Object_) {
				$this->errors->addErrorMessage(
					sprintf(
						"%s root operation must have object type, but %s type '%s' given",
						$operationType->format(),
						$typeDefinition->kind->format(),
						$type,
					),
				);
			}
		}
	}



	private function validateGeneralTypeExtensionCorrectness(): void
	{
		foreach ($this->typeExtensions as $extensionTypeDefinition) {
			if (array_key_exists($extensionTypeDefinition->name, $this->types)) {
				$this->errors->addErrorMessage(
					sprintf(
						"Type '%s' can't be defined and extended at the same time",
						$extensionTypeDefinition->name,
					),
				);

				continue;
			}

			$extendedTypeDefinition = $this->typesAvailable[$extensionTypeDefinition->name] ?? null;

			if ($extendedTypeDefinition === null) {
				$this->errors->addErrorMessage(
					sprintf(
						"Type '%s' can't be extended because it's not defined",
						$extensionTypeDefinition->name,
					),
				);

				continue;
			}

			if (!$extendedTypeDefinition instanceof $extensionTypeDefinition) {
				$this->errors->addErrorMessage(
					sprintf(
						"%s type '%s' can't be extended as %s",
						ucfirst($extendedTypeDefinition->kind->format()),
						$extensionTypeDefinition->name,
						$extensionTypeDefinition->kind->format(),
					),
				);

				continue;
			}

			match (true) {
				$extensionTypeDefinition instanceof TypeSystem\EnumTypeDefinition => $this->typeExtensionsEnum[] = $extensionTypeDefinition,
				$extensionTypeDefinition instanceof TypeSystem\InputObjectTypeDefinition => $this->typeExtensionsInput[] = $extensionTypeDefinition,
				$extensionTypeDefinition instanceof TypeSystem\InterfaceTypeDefinition => $this->typeExtensionsInterface[] = $extensionTypeDefinition,
				$extensionTypeDefinition instanceof TypeSystem\ObjectTypeDefinition => $this->typeExtensionsObject[] = $extensionTypeDefinition,
				$extensionTypeDefinition instanceof TypeSystem\ScalarTypeDefinition => $this->typeExtensionsScalar[] = $extensionTypeDefinition,
				$extensionTypeDefinition instanceof TypeSystem\UnionTypeDefinition => $this->typeExtensionsUnion[] = $extensionTypeDefinition,
			};
		}
	}



	private function validateDirectiveDefinitions(): void
	{
		foreach ($this->directiveDefinitions as $directiveDefinition) {
			$this->validateName(
				'Directive',
				$directiveDefinition->name,
			);

			$this->validateArgumentDefinitions(
				"Directive @{$directiveDefinition->name}",
				$directiveDefinition->argumentDefinitions,
			);

			if ($this->doesDirectiveDefinitionSelfReference($directiveDefinition)) {
				$this->errors->addErrorMessage(
					"Directive @{$directiveDefinition->name} can't reference itself directly or indirectly",
				);
			}
		}
	}



	private function doesDirectiveDefinitionSelfReference(
		TypeSystem\DirectiveDefinition $directiveDefinition,
	): bool
	{
		$argumentDefinitions = $directiveDefinition->argumentDefinitions;

		while ($argumentDefinitions !== []) {
			$argumentDefinition = array_shift($argumentDefinitions);

			foreach ($argumentDefinition->directives as $argumentDirective) {
				if ($argumentDirective->name === $directiveDefinition->name) {
					return true;
				}

				$argumentDirectiveDefinition = $this->directiveDefinitionsAvailable[$argumentDirective->name];

				foreach ($argumentDirectiveDefinition->argumentDefinitions as $argumentDirectiveDefinitionArgumentDefinition) {
					if (in_array($argumentDirectiveDefinitionArgumentDefinition, $argumentDefinitions, true) === false) {
						$argumentDefinitions[] = $argumentDirectiveDefinitionArgumentDefinition;
					}
				}
			}
		}

		return false;
	}



	private function validateEnumTypes(): void
	{
		foreach ($this->typesEnum as $enumTypeDefinition) {
			$this->validateName(
				'Enum type',
				$enumTypeDefinition->name,
			);

			$enumTypeLocation = "Enum type '{$enumTypeDefinition->name}'";

			Validator::validateDuplicates(
				$this->errors,
				$enumTypeLocation,
				'define value',
				$enumTypeDefinition->enumValues,
				static fn ($enumValueDefinition) => $enumValueDefinition->name,
			);

			$this->directivesUsage[] = new TypeSystem\Builder\DirectivesUsage(
				$enumTypeLocation,
				Spec\TypeSystemDirectiveLocation::Enum,
				$enumTypeDefinition->directives,
			);

			foreach ($enumTypeDefinition->enumValues as $enumValueDefinition) {
				$this->validateName(
					"{$enumTypeLocation} value",
					$enumValueDefinition->name,
				);

				$this->directivesUsage[] = new TypeSystem\Builder\DirectivesUsage(
					"{$enumTypeLocation} value '{$enumValueDefinition->name}'",
					Spec\TypeSystemDirectiveLocation::EnumValue,
					$enumValueDefinition->directives,
				);
			}
		}
	}



	private function validateAndApplyEnumTypeExtensions(): void
	{
		foreach ($this->typeExtensionsEnum as $extensionTypeDefinition) {
			$enumTypeExtensionLocation = "Enum type '{$extensionTypeDefinition->name}'";

			Validator::validateDuplicates(
				$this->errors,
				$enumTypeExtensionLocation,
				'be extended with value',
				$extensionTypeDefinition->enumValues,
				static fn ($enumValueDefinition) => $enumValueDefinition->name,
			);

			/** @var TypeSystem\EnumTypeDefinition $extendedTypeDefinition (This is already checked in validateGeneralTypeExtensionCorrectness() method) */
			$extendedTypeDefinition = $this->typesAvailable[$extensionTypeDefinition->name];

			$this->directivesUsageOnExtension[] = new TypeSystem\Builder\DirectivesUsageOnExtension(
				$enumTypeExtensionLocation,
				Spec\TypeSystemDirectiveLocation::Enum,
				$extensionTypeDefinition->directives,
				$extendedTypeDefinition->directives,
			);

			$overridenEnumValueDefinitions = array_filter(
				$extensionTypeDefinition->enumValues,
				static fn ($enumValueDefinition) => array_any(
					$extendedTypeDefinition->enumValues,
					static fn ($extendedEnumValueDefinition) => $enumValueDefinition->name === $extendedEnumValueDefinition->name,
				),
			);

			foreach ($overridenEnumValueDefinitions as $overridenEnumValueDefinition) {
				$this->errors->addErrorMessage(
					sprintf(
						"%s can't be extended with already defined value '%s'",
						$enumTypeExtensionLocation,
						$overridenEnumValueDefinition->name,
					),
				);
			}

			foreach ($extensionTypeDefinition->enumValues as $enumValueDefinition) {
				$this->validateName(
					"{$enumTypeExtensionLocation} value",
					$enumValueDefinition->name,
				);

				$this->directivesUsage[] = new TypeSystem\Builder\DirectivesUsage(
					"{$enumTypeExtensionLocation} value '{$enumValueDefinition->name}'",
					Spec\TypeSystemDirectiveLocation::EnumValue,
					$enumValueDefinition->directives,
				);
			}

			$this->typesAvailable[$extensionTypeDefinition->name] = new TypeSystem\EnumTypeDefinition(
				description: $extendedTypeDefinition->description,
				directives: [
					...$extendedTypeDefinition->directives,
					...$extensionTypeDefinition->directives,
				],
				enumValues: [
					...$extendedTypeDefinition->enumValues,
					...$extensionTypeDefinition->enumValues,
				],
				name: $extendedTypeDefinition->name,
			);
		}
	}



	private function validateAndApplyInputObjectTypeExtensions(): void
	{
		foreach ($this->typeExtensionsInput as $extensionTypeDefinition) {
			$inputObjectTypeExtensionLocation = "Input object type '{$extensionTypeDefinition->name}'";

			/** @var TypeSystem\InputObjectTypeDefinition $extendedTypeDefinition (This is already checked in validateGeneralTypeExtensionCorrectness() method) */
			$extendedTypeDefinition = $this->typesAvailable[$extensionTypeDefinition->name];

			$this->directivesUsageOnExtension[] = new TypeSystem\Builder\DirectivesUsageOnExtension(
				$inputObjectTypeExtensionLocation,
				Spec\TypeSystemDirectiveLocation::InputObject,
				$extensionTypeDefinition->directives,
				$extendedTypeDefinition->directives,
			);

			Validator::validateDuplicates(
				$this->errors,
				$inputObjectTypeExtensionLocation,
				'be extended to define field',
				$extensionTypeDefinition->fields,
				static fn ($fieldDefinition) => $fieldDefinition->name,
			);

			$overridenFieldDefinitions = array_filter(
				$extensionTypeDefinition->fields,
				static fn ($fieldDefinition) => array_any(
					$extendedTypeDefinition->fields,
					static fn ($extendedFieldDefinition) => $fieldDefinition->name === $extendedFieldDefinition->name,
				),
			);

			foreach ($overridenFieldDefinitions as $overridenFieldDefinition) {
				$this->errors->addErrorMessage(
					sprintf(
						"%s can't be extended to define already defined field '%s'",
						$inputObjectTypeExtensionLocation,
						$overridenFieldDefinition->name,
					),
				);
			}

			foreach ($extensionTypeDefinition->fields as $inputFieldDefinition) {
				$this->validateName(
					"{$inputObjectTypeExtensionLocation} extended field",
					$inputFieldDefinition->name,
				);

				$inputFieldLocation = "Extended input object field '{$extensionTypeDefinition->name}.{$inputFieldDefinition->name}'";

				$this->validator->validateInputType(
					$this->errors,
					$inputFieldLocation,
					$inputFieldDefinition->type,
				);

				$this->directivesUsage[] = new TypeSystem\Builder\DirectivesUsage(
					$inputFieldLocation,
					Spec\TypeSystemDirectiveLocation::InputFieldDefinition,
					$inputFieldDefinition->directives,
				);

				if ($inputFieldDefinition->defaultValue !== null) {
					$this->validator->validateValue(
						$this->errors,
						sprintf('Default value of %s', lcfirst($inputFieldLocation)),
						$inputFieldDefinition->type,
						$inputFieldDefinition->defaultValue,
					);
				}
			}

			$this->typesAvailable[$extensionTypeDefinition->name] = new TypeSystem\InputObjectTypeDefinition(
				description: $extendedTypeDefinition->description,
				directives: [
					...$extendedTypeDefinition->directives,
					...$extensionTypeDefinition->directives,
				],
				fields: [
					...$extendedTypeDefinition->fields,
					...$extensionTypeDefinition->fields,
				],
				name: $extendedTypeDefinition->name,
			);
		}

		foreach ($this->typeExtensionsInput as $extensionTypeDefinition) {
			$inputObjectTypeExtensionLocation = "Extended input object type '{$extensionTypeDefinition->name}'";

			foreach ($extensionTypeDefinition->fields as $inputFieldDefinition) {
				if ($this->doesInputObjectTypeFieldInvalidlySelfReference($extensionTypeDefinition, $inputFieldDefinition)) {
					$this->errors->addErrorMessage(
						sprintf(
							"%s can't reference itself in field '%s' by a chain of non-nullable fields",
							$inputObjectTypeExtensionLocation,
							$inputFieldDefinition->name,
						),
					);
				}
			}
		}
	}



	private function validateAndApplyInterfaceTypeExtensions(): void
	{
		foreach ($this->typeExtensionsInterface as $extensionTypeDefinition) {
			$interfaceTypeExtensionLocation = "Interface type '{$extensionTypeDefinition->name}'";

			/** @var TypeSystem\InterfaceTypeDefinition $extendedTypeDefinition (This is already checked in validateGeneralTypeExtensionCorrectness() method) */
			$extendedTypeDefinition = $this->typesAvailable[$extensionTypeDefinition->name];

			$this->directivesUsageOnExtension[] = new TypeSystem\Builder\DirectivesUsageOnExtension(
				$interfaceTypeExtensionLocation,
				Spec\TypeSystemDirectiveLocation::Interface_,
				$extensionTypeDefinition->directives,
				$extendedTypeDefinition->directives,
			);

			foreach ($extensionTypeDefinition->implementedInterfaces as $interface) {
				$implementedTypeDefinition = $this->typesAvailable[$interface] ?? null;

				if ($implementedTypeDefinition === null) {
					$this->errors->addErrorMessage(
						sprintf(
							"%s can't be extended to implement unknown type '%s'",
							$interfaceTypeExtensionLocation,
							$interface,
						),
					);
				} elseif (!$implementedTypeDefinition instanceof TypeSystem\InterfaceTypeDefinition) {
					$this->errors->addErrorMessage(
						sprintf(
							"%s can't be extended to implement %s type '%s'",
							$interfaceTypeExtensionLocation,
							$implementedTypeDefinition->kind->format(),
							$interface,
						),
					);
				}
			}

			Validator::validateDuplicates(
				$this->errors,
				$interfaceTypeExtensionLocation,
				'be extended to define field',
				$extensionTypeDefinition->fields,
				static fn ($field) => $field->name,
			);

			foreach ($extensionTypeDefinition->fields as $fieldDefinition) {
				if (array_key_exists($fieldDefinition->name, $extendedTypeDefinition->fieldsByName)) {
					$this->errors->addErrorMessage(
						sprintf(
							"%s can't be extended to override already defined field '%s'",
							$interfaceTypeExtensionLocation,
							$fieldDefinition->name,
						),
					);

					continue;
				}

				$this->validateName(
					"{$interfaceTypeExtensionLocation} field",
					$fieldDefinition->name,
				);

				$interfaceTypeFieldLocation = "Extended interface type field '{$extensionTypeDefinition->name}.{$fieldDefinition->name}'";

				$this->validateArgumentDefinitions(
					$interfaceTypeFieldLocation,
					$fieldDefinition->argumentDefinitions,
				);

				$this->validator->validateOutputType(
					$this->errors,
					$interfaceTypeFieldLocation,
					$fieldDefinition->type,
				);

				$this->directivesUsage[] = new TypeSystem\Builder\DirectivesUsage(
					$interfaceTypeFieldLocation,
					Spec\TypeSystemDirectiveLocation::FieldDefinition,
					$fieldDefinition->directives,
				);
			}

			$this->typesAvailable[$extensionTypeDefinition->name] = new TypeSystem\InterfaceTypeDefinition(
				description: $extendedTypeDefinition->description,
				directives: [
					...$extendedTypeDefinition->directives,
					...$extensionTypeDefinition->directives,
				],
				fields: [
					...$extendedTypeDefinition->fields,
					...$extensionTypeDefinition->fields,
				],
				implementedInterfaces: [
					...$extendedTypeDefinition->implementedInterfaces,
					...$extensionTypeDefinition->implementedInterfaces,
				],
				name: $extendedTypeDefinition->name,
			);
		}

		foreach ($this->typeExtensionsInterface as $extensionTypeDefinition) {
			$interfaceTypeExtensionLocation = "Interface type '{$extensionTypeDefinition->name}'";

			if ($this->doesImplementedInterfacesSelfReference($extensionTypeDefinition)) {
				$this->errors->addErrorMessage(
					sprintf(
						"%s can't be extended to implement itself directly or indirectly",
						$interfaceTypeExtensionLocation,
					),
				);
			}

			foreach ($extensionTypeDefinition->implementedInterfaces as $interface) {
				$implementedTypeDefinition = $this->typesAvailable[$interface] ?? null;

				if ($implementedTypeDefinition instanceof TypeSystem\InterfaceTypeDefinition) {
					foreach ($implementedTypeDefinition->implementedInterfaces as $transitivelyImplementedInterface) {
						if (
							$transitivelyImplementedInterface === $extensionTypeDefinition->name
							|| !($this->typesAvailable[$transitivelyImplementedInterface] ?? null) instanceof TypeSystem\InterfaceTypeDefinition
						) {
							continue;
						}

						if (in_array($transitivelyImplementedInterface, $extensionTypeDefinition->implementedInterfaces, true) === false) {
							$this->errors->addErrorMessage(
								sprintf(
									"%s must be also extended to directly implement interface '%s' because implemented interface '%s' implements it",
									$interfaceTypeExtensionLocation,
									$transitivelyImplementedInterface,
									$implementedTypeDefinition->name,
								),
							);
						}
					}
				}
			}
		}
	}



	private function validateAndApplyObjectTypeExtensions(): void
	{
		foreach ($this->typeExtensionsObject as $extensionTypeDefinition) {
			$objectTypeExtensionLocation = "Object type '{$extensionTypeDefinition->name}'";

			/** @var TypeSystem\ObjectTypeDefinition $extendedTypeDefinition (This is already checked in validateGeneralTypeExtensionCorrectness() method) */
			$extendedTypeDefinition = $this->typesAvailable[$extensionTypeDefinition->name];

			$this->directivesUsageOnExtension[] = new TypeSystem\Builder\DirectivesUsageOnExtension(
				$objectTypeExtensionLocation,
				Spec\TypeSystemDirectiveLocation::Object_,
				$extensionTypeDefinition->directives,
				$extendedTypeDefinition->directives,
			);

			foreach ($extensionTypeDefinition->implementedInterfaces as $interface) {
				$implementedTypeDefinition = $this->typesAvailable[$interface] ?? null;

				if ($implementedTypeDefinition === null) {
					$this->errors->addErrorMessage(
						sprintf(
							"%s can't be extended to implement unknown type '%s'",
							$objectTypeExtensionLocation,
							$interface,
						),
					);

					continue;
				} elseif (!$implementedTypeDefinition instanceof TypeSystem\InterfaceTypeDefinition) {
					$this->errors->addErrorMessage(
						sprintf(
							"%s can't be extended to implement %s type '%s'",
							$objectTypeExtensionLocation,
							$implementedTypeDefinition->kind->format(),
							$interface,
						),
					);

					continue;
				} else {
					foreach ($implementedTypeDefinition->implementedInterfaces as $transitivelyImplementedInterface) {
						if (!($this->typesAvailable[$transitivelyImplementedInterface] ?? null) instanceof TypeSystem\InterfaceTypeDefinition) {
							continue;
						}

						if (in_array($transitivelyImplementedInterface, $extensionTypeDefinition->implementedInterfaces, true) === false) {
							$this->errors->addErrorMessage(
								sprintf(
									"%s must be also extended to directly implement interface '%s' because implemented interface '%s' implements it",
									$objectTypeExtensionLocation,
									$transitivelyImplementedInterface,
									$implementedTypeDefinition->name,
								),
							);
						}
					}
				}
			}

			Validator::validateDuplicates(
				$this->errors,
				$objectTypeExtensionLocation,
				'be extended to define field',
				$extensionTypeDefinition->fields,
				static fn ($field) => $field->name,
			);

			foreach ($extensionTypeDefinition->fields as $fieldDefinition) {
				if (array_key_exists($fieldDefinition->name, $extendedTypeDefinition->fieldsByName)) {
					$this->errors->addErrorMessage(
						sprintf(
							"%s can't be extended to override already defined field '%s'",
							$objectTypeExtensionLocation,
							$fieldDefinition->name,
						),
					);

					continue;
				}

				$this->validateName(
					"{$objectTypeExtensionLocation} field",
					$fieldDefinition->name,
				);

				$objectTypeFieldLocation = "Extended object type field '{$extensionTypeDefinition->name}.{$fieldDefinition->name}'";

				$this->validateArgumentDefinitions(
					$objectTypeFieldLocation,
					$fieldDefinition->argumentDefinitions,
				);

				$this->validator->validateOutputType(
					$this->errors,
					$objectTypeFieldLocation,
					$fieldDefinition->type,
				);

				$this->directivesUsage[] = new TypeSystem\Builder\DirectivesUsage(
					$objectTypeFieldLocation,
					Spec\TypeSystemDirectiveLocation::FieldDefinition,
					$fieldDefinition->directives,
				);
			}

			$this->typesAvailable[$extensionTypeDefinition->name] = new TypeSystem\ObjectTypeDefinition(
				description: $extendedTypeDefinition->description,
				directives: [
					...$extendedTypeDefinition->directives,
					...$extensionTypeDefinition->directives,
				],
				fields: [
					...$extendedTypeDefinition->fields,
					...$extensionTypeDefinition->fields,
				],
				implementedInterfaces: [
					...$extendedTypeDefinition->implementedInterfaces,
					...$extensionTypeDefinition->implementedInterfaces,
				],
				name: $extendedTypeDefinition->name,
			);
		}
	}



	private function validateAndApplyScalarTypeExtensions(): void
	{
		foreach ($this->typeExtensionsScalar as $extensionTypeDefinition) {
			/** @var TypeSystem\ScalarTypeDefinition $extendedTypeDefinition (This is already checked in validateGeneralTypeExtensionCorrectness() method) */
			$extendedTypeDefinition = $this->typesAvailable[$extensionTypeDefinition->name];

			$this->directivesUsageOnExtension[] = new TypeSystem\Builder\DirectivesUsageOnExtension(
				"Scalar type '{$extensionTypeDefinition->name}'",
				Spec\TypeSystemDirectiveLocation::Scalar,
				$extensionTypeDefinition->directives,
				$extendedTypeDefinition->directives,
			);

			$this->typesAvailable[$extensionTypeDefinition->name] = new TypeSystem\ScalarTypeDefinition(
				description: $extendedTypeDefinition->description,
				directives: [
					...$extendedTypeDefinition->directives,
					...$extensionTypeDefinition->directives,
				],
				name: $extendedTypeDefinition->name,
			);
		}
	}



	private function validateAndApplyUnionTypeExtensions(): void
	{
		foreach ($this->typeExtensionsUnion as $extensionTypeDefinition) {
			$unionTypeExtensionLocation = "Union type '{$extensionTypeDefinition->name}'";

			/** @var TypeSystem\UnionTypeDefinition $extendedTypeDefinition (This is already checked in validateGeneralTypeExtensionCorrectness() method) */
			$extendedTypeDefinition = $this->typesAvailable[$extensionTypeDefinition->name];

			$this->directivesUsageOnExtension[] = new TypeSystem\Builder\DirectivesUsageOnExtension(
				$unionTypeExtensionLocation,
				Spec\TypeSystemDirectiveLocation::Union,
				$extensionTypeDefinition->directives,
				$extendedTypeDefinition->directives,
			);

			Validator::validateDuplicates(
				$this->errors,
				$unionTypeExtensionLocation,
				'be extended to include type',
				$extensionTypeDefinition->possibleTypes,
				static fn ($possibleType) => $possibleType,
			);

			$overridenPossibleTypes = array_intersect(
				$extensionTypeDefinition->possibleTypes,
				$extendedTypeDefinition->possibleTypes,
			);

			foreach ($overridenPossibleTypes as $overridenPossibleType) {
				$this->errors->addErrorMessage(
					sprintf(
						"%s can't be extended to include already included type '%s'",
						$unionTypeExtensionLocation,
						$overridenPossibleType,
					),
				);
			}

			foreach ($extensionTypeDefinition->possibleTypes as $possibleType) {
				$possibleTypeDefinition = $this->typesAvailable[$possibleType] ?? null;

				if ($possibleTypeDefinition === null) {
					$this->errors->addErrorMessage(
						sprintf(
							"%s can't be extended to include unknown type '%s'",
							$unionTypeExtensionLocation,
							$possibleType,
						),
					);
				} elseif ($possibleTypeDefinition->kind !== TypeSystem\TypeKind::Object_) {
					$this->errors->addErrorMessage(
						sprintf(
							"%s can't be extended to include %s type '%s'",
							$unionTypeExtensionLocation,
							$possibleTypeDefinition->kind->format(),
							$possibleType,
						),
					);
				}
			}

			$this->typesAvailable[$extensionTypeDefinition->name] = new TypeSystem\UnionTypeDefinition(
				description: $extendedTypeDefinition->description,
				directives: [
					...$extendedTypeDefinition->directives,
					...$extensionTypeDefinition->directives,
				],
				possibleTypes: [
					...$extendedTypeDefinition->possibleTypes,
					...$extensionTypeDefinition->possibleTypes,
				],
				name: $extendedTypeDefinition->name,
			);
		}
	}



	private function validateInputObjectTypes(): void
	{
		foreach ($this->typesInput as $inputTypeDefinition) {
			$this->validateName(
				'Input object type',
				$inputTypeDefinition->name,
			);

			$inputObjectTypeLocation = "Input object type '{$inputTypeDefinition->name}'";

			$this->directivesUsage[] = new TypeSystem\Builder\DirectivesUsage(
				$inputObjectTypeLocation,
				Spec\TypeSystemDirectiveLocation::InputObject,
				$inputTypeDefinition->directives,
			);

			Validator::validateDuplicates(
				$this->errors,
				$inputObjectTypeLocation,
				'define field',
				$inputTypeDefinition->fields,
				static fn ($field) => $field->name,
			);

			foreach ($inputTypeDefinition->fields as $inputFieldDefinition) {
				$this->validateName(
					"{$inputObjectTypeLocation} field",
					$inputFieldDefinition->name,
				);

				$inputFieldLocation = "Input object field '{$inputTypeDefinition->name}.{$inputFieldDefinition->name}'";

				$this->validator->validateInputType(
					$this->errors,
					$inputFieldLocation,
					$inputFieldDefinition->type,
				);

				$this->directivesUsage[] = new TypeSystem\Builder\DirectivesUsage(
					$inputFieldLocation,
					Spec\TypeSystemDirectiveLocation::InputFieldDefinition,
					$inputFieldDefinition->directives,
				);

				if ($this->doesInputObjectTypeFieldInvalidlySelfReference($inputTypeDefinition, $inputFieldDefinition)) {
					$this->errors->addErrorMessage(
						sprintf(
							"%s can't reference itself in field '%s' by a chain of non-nullable fields",
							$inputObjectTypeLocation,
							$inputFieldDefinition->name,
						),
					);
				}

				if ($inputFieldDefinition->defaultValue !== null) {
					$this->validator->validateValue(
						$this->errors,
						sprintf('Default value of %s', lcfirst($inputFieldLocation)),
						$inputFieldDefinition->type,
						$inputFieldDefinition->defaultValue,
					);
				}
			}
		}
	}



	private function doesInputObjectTypeFieldInvalidlySelfReference(
		TypeSystem\InputObjectTypeDefinition $inputObjectTypeDefinition,
		TypeSystem\InputValueDefinition $rootInputFieldDefinition,
	): bool
	{
		$inputFieldDefinitions = [$rootInputFieldDefinition];

		$result = [];

		while ($inputFieldDefinitions !== []) {
			$inputFieldDefinition = array_shift($inputFieldDefinitions);
			$inputFieldTypeDefinition = $this->typesAvailable[$inputFieldDefinition->type->getNamedType()] ?? null;

			if (
				$inputFieldTypeDefinition === null
				|| !$inputFieldTypeDefinition instanceof TypeSystem\InputObjectTypeDefinition
			) {
				continue;
			}

			$isNonNullable = false;
			$type = $inputFieldDefinition->type;

			while ($type !== null) {
				if ($type instanceof Types\ListType) {
					continue 2;
				} elseif ($type instanceof Types\NamedType) {
					break;
				} else { // $type instanceof Types\NonNullType
					$isNonNullable = true;
				}

				$type = $type->getWrappedType();
			}

			if ($isNonNullable === false) {
				continue;
			}

			if ($inputFieldTypeDefinition->name === $inputObjectTypeDefinition->name) {
				return true;
			}

			foreach ($inputFieldTypeDefinition->fields as $potentialInputFieldDefinition) {
				if (in_array($potentialInputFieldDefinition, $inputFieldDefinitions, true) === false) {
					$inputFieldDefinitions[] = $potentialInputFieldDefinition;
				}
			}
		}

		return false;
	}



	private function validateInterfaceTypes(): void
	{
		foreach ($this->typesInterface as $interfaceTypeDefinition) {
			$this->validateName(
				'Interface type',
				$interfaceTypeDefinition->name,
			);

			$interfaceTypeLocation = "Interface type '{$interfaceTypeDefinition->name}'";

			$this->directivesUsage[] = new TypeSystem\Builder\DirectivesUsage(
				$interfaceTypeLocation,
				Spec\TypeSystemDirectiveLocation::Interface_,
				$interfaceTypeDefinition->directives,
			);

			Validator::validateDuplicates(
				$this->errors,
				$interfaceTypeLocation,
				'define field',
				$interfaceTypeDefinition->fields,
				static fn ($field) => $field->name,
			);

			foreach ($interfaceTypeDefinition->implementedInterfaces as $interface) {
				$implementedTypeDefinition = $this->typesAvailable[$interface] ?? null;

				if ($implementedTypeDefinition === null) {
					$this->errors->addErrorMessage(
						sprintf(
							"%s can't implement unknown type '%s'",
							$interfaceTypeLocation,
							$interface,
						),
					);
				} elseif (!$implementedTypeDefinition instanceof TypeSystem\InterfaceTypeDefinition) {
					$this->errors->addErrorMessage(
						sprintf(
							"%s can't implement %s type '%s'",
							$interfaceTypeLocation,
							$implementedTypeDefinition->kind->format(),
							$interface,
						),
					);
				} else {
					foreach ($implementedTypeDefinition->implementedInterfaces as $transitivelyImplementedInterface) {
						if (
							$transitivelyImplementedInterface === $interfaceTypeDefinition->name
							|| !($this->typesAvailable[$transitivelyImplementedInterface] ?? null) instanceof TypeSystem\InterfaceTypeDefinition
						) {
							continue;
						}

						if (in_array($transitivelyImplementedInterface, $interfaceTypeDefinition->implementedInterfaces, true) === false) {
							$this->errors->addErrorMessage(
								sprintf(
									"%s must directly implement interface '%s' because implemented interface '%s' implements it",
									$interfaceTypeLocation,
									$transitivelyImplementedInterface,
									$implementedTypeDefinition->name,
								),
							);
						}
					}
				}
			}

			foreach ($interfaceTypeDefinition->fields as $fieldDefinition) {
				$this->validateName(
					"{$interfaceTypeLocation} field",
					$fieldDefinition->name,
				);

				$interfaceTypeFieldLocation = "Interface type field '{$interfaceTypeDefinition->name}.{$fieldDefinition->name}'";

				$this->validateArgumentDefinitions(
					$interfaceTypeFieldLocation,
					$fieldDefinition->argumentDefinitions,
				);

				$this->validator->validateOutputType(
					$this->errors,
					$interfaceTypeFieldLocation,
					$fieldDefinition->type,
				);

				$this->directivesUsage[] = new TypeSystem\Builder\DirectivesUsage(
					$interfaceTypeFieldLocation,
					Spec\TypeSystemDirectiveLocation::FieldDefinition,
					$fieldDefinition->directives,
				);
			}

			if ($this->doesImplementedInterfacesSelfReference($interfaceTypeDefinition)) {
				$this->errors->addErrorMessage(
					sprintf(
						"%s can't implement itself directly or indirectly",
						$interfaceTypeLocation,
					),
				);
			}
		}
	}



	private function doesImplementedInterfacesSelfReference(
		TypeSystem\InterfaceTypeDefinition $interfaceTypeDefinition,
	): bool
	{
		$implementedInterfaces = $interfaceTypeDefinition->implementedInterfaces;

		while ($implementedInterfaces !== []) {
			$implementedInterface = array_pop($implementedInterfaces);

			$implementedInterfaceTypeDefinition = $this->typesAvailable[$implementedInterface] ?? null;

			if (!$implementedInterfaceTypeDefinition instanceof TypeSystem\InterfaceTypeDefinition) {
				continue;
			}

			if ($implementedInterfaceTypeDefinition->name === $interfaceTypeDefinition->name) {
				return true;
			}

			foreach ($implementedInterfaceTypeDefinition->implementedInterfaces as $potentialFurtherImplementedInterface) {
				if (in_array($potentialFurtherImplementedInterface, $implementedInterfaces, true) === false) {
					$implementedInterfaces[] = $potentialFurtherImplementedInterface;
				}
			}
		}

		return false;
	}



	private function validateName(
		string $location,
		string $name,
	): void
	{
		if ($this->allowReservedNames) {
			return;
		}

		if (str_starts_with($name, '__')) {
			$this->errors->addErrorMessage(
				sprintf(
					"%s name '%s' can't begin with '__', because such names are reserved for introspection",
					$location,
					$name,
				),
			);
		}
	}



	private function validateObjectTypes(): void
	{
		foreach ($this->typesObject as $objectTypeDefinition) {
			$this->validateName(
				'Object type',
				$objectTypeDefinition->name,
			);

			$objectTypeLocation = "Object type '{$objectTypeDefinition->name}'";

			$this->directivesUsage[] = new TypeSystem\Builder\DirectivesUsage(
				$objectTypeLocation,
				Spec\TypeSystemDirectiveLocation::Object_,
				$objectTypeDefinition->directives,
			);

			Validator::validateDuplicates(
				$this->errors,
				$objectTypeLocation,
				'define field',
				$objectTypeDefinition->fields,
				static fn ($field) => $field->name,
			);

			foreach ($objectTypeDefinition->implementedInterfaces as $interface) {
				$implementedTypeDefinition = $this->typesAvailable[$interface] ?? null;

				if ($implementedTypeDefinition === null) {
					$this->errors->addErrorMessage(
						sprintf(
							"%s can't implement unknown type '%s'",
							$objectTypeLocation,
							$interface,
						),
					);

					continue;
				} elseif (!$implementedTypeDefinition instanceof TypeSystem\InterfaceTypeDefinition) {
					$this->errors->addErrorMessage(
						sprintf(
							"%s can't implement %s type '%s'",
							$objectTypeLocation,
							$implementedTypeDefinition->kind->format(),
							$interface,
						),
					);

					continue;
				} else {
					foreach ($implementedTypeDefinition->implementedInterfaces as $transitivelyImplementedInterface) {
						if (!($this->typesAvailable[$transitivelyImplementedInterface] ?? null) instanceof TypeSystem\InterfaceTypeDefinition) {
							continue;
						}

						if (in_array($transitivelyImplementedInterface, $objectTypeDefinition->implementedInterfaces, true) === false) {
							$this->errors->addErrorMessage(
								sprintf(
									"%s must directly implement interface '%s' because implemented interface '%s' implements it",
									$objectTypeLocation,
									$transitivelyImplementedInterface,
									$implementedTypeDefinition->name,
								),
							);
						}
					}
				}

				foreach ($implementedTypeDefinition->fields as $interfaceFieldDefinition) {
					$objectFieldDefinition = $objectTypeDefinition->fieldsByName[$interfaceFieldDefinition->name] ?? null;

					if ($objectFieldDefinition === null) {
						$this->errors->addErrorMessage(
							sprintf(
								"Object type '%s' implements interface '%s' but doesn't have field '%s'",
								$objectTypeDefinition->name,
								$implementedTypeDefinition->name,
								$interfaceFieldDefinition->name,
							),
						);

						continue;
					}

					if ($this->validator->validateFieldCovariance($objectFieldDefinition, $interfaceFieldDefinition) === false) {
						$this->errors->addErrorMessage(
							sprintf(
								"Object type field '%s.%s' of type %s isn't covariant with interface type field '%s.%s' of type %s",
								$objectTypeDefinition->name,
								$interfaceFieldDefinition->name,
								$objectFieldDefinition->type->format(),
								$implementedTypeDefinition->name,
								$interfaceFieldDefinition->name,
								$interfaceFieldDefinition->type->format(),
							),
						);
					}
				}
			}

			foreach ($objectTypeDefinition->fields as $fieldDefinition) {
				$this->validateName(
					"{$objectTypeLocation} field",
					$fieldDefinition->name,
				);

				$objectTypeFieldLocation = "Object type field '{$objectTypeDefinition->name}.{$fieldDefinition->name}'";

				$this->validateArgumentDefinitions(
					$objectTypeFieldLocation,
					$fieldDefinition->argumentDefinitions,
				);

				$this->validator->validateOutputType(
					$this->errors,
					$objectTypeFieldLocation,
					$fieldDefinition->type,
				);

				$this->directivesUsage[] = new TypeSystem\Builder\DirectivesUsage(
					$objectTypeFieldLocation,
					Spec\TypeSystemDirectiveLocation::FieldDefinition,
					$fieldDefinition->directives,
				);
			}
		}
	}



	private function validateRootOperationTypes(): void
	{
		if ($this->rootOperationTypes === null) {
			return;
		}

		if ($this->rootOperationTypesBase !== null) {
			$this->errors->addErrorMessage(
				"Schema extension can't define 'schema' block because it's already defined in base schema",
			);

			return;
		}

		Validator::validateDuplicates(
			$this->errors,
			'Schema definition',
			'define root operation type',
			$this->rootOperationTypes->types,
			static fn ($rootOperationType) => $rootOperationType['operationType']->value,
		);

		$this->directivesUsage[] = new TypeSystem\Builder\DirectivesUsage(
			'Schema definition',
			Spec\TypeSystemDirectiveLocation::Schema,
			$this->rootOperationTypes->directives,
		);

		foreach ($this->rootOperationTypes->types as $rootOperationType) {
			$type = $rootOperationType['type'];
			$operationType = $rootOperationType['operationType'];

			$typeDefinition = $this->typesAvailable[$type] ?? null;

			if ($typeDefinition === null) {
				$this->errors->addErrorMessage(
					sprintf(
						"%s root operation has unknown type '%s'",
						$operationType->format(),
						$type,
					),
				);

				continue;
			}

			if ($typeDefinition->kind !== TypeSystem\TypeKind::Object_) {
				$this->errors->addErrorMessage(
					sprintf(
						"%s root operation must have object type, but %s type '%s' given",
						$operationType->format(),
						$typeDefinition->kind->format(),
						$type,
					),
				);
			}
		}
	}



	private function validateScalarTypes(): void
	{
		foreach ($this->typesScalar as $scalarTypeDefinition) {
			$this->validateName(
				'Scalar type',
				$scalarTypeDefinition->name,
			);

			$this->directivesUsage[] = new TypeSystem\Builder\DirectivesUsage(
				"Scalar type '{$scalarTypeDefinition->name}'",
				Spec\TypeSystemDirectiveLocation::Scalar,
				$scalarTypeDefinition->directives,
			);
		}
	}



	private function validateUnionTypes(): void
	{
		foreach ($this->typesUnion as $unionTypeDefinition) {
			$this->validateName(
				'Union type',
				$unionTypeDefinition->name,
			);

			$unionTypeLocation = "Union type '{$unionTypeDefinition->name}'";

			$this->directivesUsage[] = new TypeSystem\Builder\DirectivesUsage(
				$unionTypeLocation,
				Spec\TypeSystemDirectiveLocation::Union,
				$unionTypeDefinition->directives,
			);

			Validator::validateDuplicates(
				$this->errors,
				$unionTypeLocation,
				'include type',
				$unionTypeDefinition->possibleTypes,
				static fn ($possibleType) => $possibleType,
			);

			foreach ($unionTypeDefinition->possibleTypes as $possibleType) {
				$possibleTypeDefinition = $this->typesAvailable[$possibleType] ?? null;

				if ($possibleTypeDefinition === null) {
					$this->errors->addErrorMessage(
						sprintf(
							"%s can't include unknown type '%s'",
							$unionTypeLocation,
							$possibleType,
						),
					);
				} elseif ($possibleTypeDefinition->kind !== TypeSystem\TypeKind::Object_) {
					$this->errors->addErrorMessage(
						sprintf(
							"%s can't include %s type '%s'",
							$unionTypeLocation,
							$possibleTypeDefinition->kind->format(),
							$possibleType,
						),
					);
				}
			}
		}
	}



	private function validateDirectiveUsage(): void
	{
		foreach ($this->directivesUsage as $directivesUsage) {
			$this->validator->validateDirectives(
				$this->errors,
				[],
				$directivesUsage->location,
				$directivesUsage->allowedDirectiveLocation,
				$directivesUsage->directives,
			);
		}

		foreach ($this->directivesUsageOnExtension as $directivesUsageOnExtension) {
			$this->validator->validateDirectivesOnExtension(
				$this->errors,
				$directivesUsageOnExtension->location,
				$directivesUsageOnExtension->allowedDirectiveLocation,
				$directivesUsageOnExtension->directives,
				$directivesUsageOnExtension->baseDirectives,
			);
		}
	}

}
