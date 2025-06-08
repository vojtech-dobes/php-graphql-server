<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL;


final class Validator
{

	/**
	 * @param TypeSystem\Registry<TypeSystem\DirectiveDefinition> $directiveDefinitionRegistry
	 * @param TypeSystem\Registry<ScalarImplementation<mixed, mixed>> $scalarImplementationRegistry
	 * @param TypeSystem\Registry<TypeSystem\TypeDefinition> $typeDefinitionRegistry
	 */
	public function __construct(
		private readonly TypeSystem\Registry $directiveDefinitionRegistry,
		private readonly TypeSystem\Registry $scalarImplementationRegistry,
		private readonly TypeSystem\Registry $typeDefinitionRegistry,
	) {}



	/**
	 * @param array<string, Executable\VariableDefinition> $variableDefinitions
	 * @param list<TypeSystem\InputValueDefinition> $argumentDefinitions
	 * @param list<Executable\Argument<Values\Value<mixed>|Executable\Variable>> $arguments
	 */
	public function validateArguments(
		Errors $errors,
		array $variableDefinitions,
		string $location,
		array $argumentDefinitions,
		array $arguments,
	): void
	{
		self::validateDuplicates(
			$errors,
			$location,
			'accept argument',
			$arguments,
			static fn ($argument) => $argument->name,
		);

		foreach ($arguments as $argument) {
			$argumentDefinition = array_find(
				$argumentDefinitions,
				static fn ($argumentDefinition) => $argumentDefinition->name === $argument->name,
			);

			if ($argumentDefinition === null) {
				$errors->addErrorMessage(
					sprintf(
						"%s can't accept unknown argument '%s'",
						$location,
						$argument->name,
					),
				);

				continue;
			}

			$argumentLocation = sprintf(
				"Argument '%s' of %s",
				$argument->name,
				$location,
			);

			if ($argument->value instanceof Executable\Variable) {
				$variableDefinition = $variableDefinitions[$argument->value->name] ?? null;

				if ($variableDefinition === null) {
					$errors->addErrorMessage(
						sprintf(
							"%s references unknown variable '%s'",
							$argumentLocation,
							$argument->value->name,
						),
					);

					continue;
				}

				/** @var ?Values\Value<mixed> $variableDefaultValue (This is already ensured in Validation of the Variable definition) */
				$variableDefaultValue = $variableDefinition->defaultValue;

				$isValid = $this->validateVariableArgument(
					locationDefaultValue: $argumentDefinition->defaultValue,
					locationType: $argumentDefinition->type,
					variableDefaultValue: $variableDefaultValue,
					variableType: $variableDefinition->type,
				);

				if ($isValid === false) {
					$errors->addErrorMessage(
						sprintf(
							"%s must be %s, but %s variable '%s' given",
							$argumentLocation,
							$argumentDefinition->type->format(),
							$variableDefinition->type->format(),
							$argument->value->name,
						),
					);
				}

				continue;
			}

			$this->validateValue(
				$errors,
				$argumentLocation,
				$argumentDefinition->type,
				$argument->value,
			);
		}

		foreach ($argumentDefinitions as $argumentDefinition) {
			if (
				!$argumentDefinition->type instanceof Types\NonNullType
				|| $argumentDefinition->defaultValue !== null
			) {
				continue;
			}

			$argument = array_find(
				$arguments,
				static fn ($argument) => $argument->name === $argumentDefinition->name,
			);

			if ($argument === null) {
				$argumentLocation = sprintf(
					"Argument '%s' of %s",
					$argumentDefinition->name,
					$location,
				);

				$errors->addErrorMessage(
					sprintf(
						'%s must be %s, but no value given',
						$argumentLocation,
						$argumentDefinition->type->format(),
					),
				);
			}
		}
	}



	/**
	 * @param array<string, Executable\VariableDefinition> $variableDefinitions
	 * @param list<Executable\Directive<Values\Value<mixed>|Executable\Variable>> $directives
	 */
	public function validateDirectives(
		Errors $errors,
		array $variableDefinitions,
		string $location,
		Spec\ExecutableDirectiveLocation|Spec\TypeSystemDirectiveLocation $allowedLocation,
		array $directives,
	): void
	{
		$groupedDirectives = [];

		foreach ($directives as $directive) {
			$groupedDirectives[$directive->name][] = $directive;
		}

		foreach ($groupedDirectives as $directiveName => $groupDirectives) {
			$directiveDefinition = $this->directiveDefinitionRegistry->getItemOrNull($directiveName);

			if ($directiveDefinition === null) {
				foreach ($groupedDirectives as $directive) {
					$errors->addErrorMessage(
						sprintf(
							"Directive @%s on %s isn't defined",
							$directiveName,
							lcfirst($location),
						),
					);
				}

				continue;
			}

			if (in_array($allowedLocation, $directiveDefinition->locations, true) === false) {
				foreach ($groupedDirectives as $directive) {
					$errors->addErrorMessage(
						sprintf(
							"Directive @%s isn't allowed to be placed on %s",
							$directiveName,
							lcfirst($location),
						),
					);
				}

				continue;
			}

			if (
				$directiveDefinition->isRepeatable === false
				&& count($groupDirectives) > 1
			) {
				$errors->addErrorMessage(
					sprintf(
						"Directive @%s on %s can't be repeated",
						$directiveName,
						lcfirst($location),
					),
				);

				$groupDirectives = [$groupDirectives[0]];
			}

			foreach ($groupDirectives as $i => $groupDirective) {
				$directiveLocation = sprintf(
					'Directive @%s%s on %s',
					$directiveName,
					count($groupDirectives) > 1
						? sprintf(' #%s', $i + 1)
						: '',
					lcfirst($location),
				);

				$this->validateArguments(
					$errors,
					$variableDefinitions,
					$directiveLocation,
					$directiveDefinition->argumentDefinitions,
					$groupDirective->arguments,
				);
			}
		}
	}



	/**
	 * @param list<Executable\Directive<Values\Value<mixed>>> $directives
	 * @param list<Executable\Directive<Values\Value<mixed>>> $baseDirectives
	 */
	public function validateDirectivesOnExtension(
		Errors $errors,
		string $location,
		Spec\TypeSystemDirectiveLocation $allowedLocation,
		array $directives,
		array $baseDirectives,
	): void
	{
		$groupedDirectives = [];

		foreach ($directives as $directive) {
			$groupedDirectives[$directive->name][] = $directive;
		}

		$groupedBaseDirectives = [];

		foreach ($baseDirectives as $baseDirective) {
			$groupedBaseDirectives[$baseDirective->name][] = $baseDirective;
		}

		foreach ($groupedDirectives as $directiveName => $groupDirectives) {
			$directiveDefinition = $this->directiveDefinitionRegistry->getItemOrNull($directiveName);

			if ($directiveDefinition === null) {
				foreach ($groupedDirectives as $directive) {
					$errors->addErrorMessage(
						sprintf(
							"%s can't be extended with unknown directive @%s",
							ucfirst($location),
							$directiveName,
						),
					);
				}

				continue;
			}

			if (in_array($allowedLocation, $directiveDefinition->locations, true) === false) {
				foreach ($groupedDirectives as $directive) {
					$errors->addErrorMessage(
						sprintf(
							"Directive @%s isn't allowed to be placed on %s",
							$directiveName,
							lcfirst($location),
						),
					);
				}

				continue;
			}

			if ($directiveDefinition->isRepeatable === false) {
				if (array_key_exists($directiveDefinition->name, $groupedBaseDirectives)) {
					$errors->addErrorMessage(
						sprintf(
							"%s can't be extended with already configured non-repeatable directive @%s",
							ucfirst($location),
							$directiveName,
						),
					);

					$groupDirectives = [];
				} elseif (count($groupDirectives) > 1) {
					$errors->addErrorMessage(
						sprintf(
							"Directive @%s on %s can't be repeated",
							$directiveName,
							lcfirst($location),
						),
					);

					$groupDirectives = [$groupDirectives[0]];
				}
			}

			foreach ($groupDirectives as $i => $groupDirective) {
				$directiveLocation = sprintf(
					'Directive @%s%s on %s',
					$directiveName,
					count($groupDirectives) > 1
						? sprintf(' #%s', $i + 1)
						: '',
					lcfirst($location),
				);

				$this->validateArguments(
					$errors,
					[],
					$directiveLocation,
					$directiveDefinition->argumentDefinitions,
					$groupDirective->arguments,
				);
			}
		}
	}



	/**
	 * @template TElement
	 * @param list<TElement> $elements
	 * @param callable(TElement): string $identityGetter
	 */
	public static function validateDuplicates(
		Errors $errors,
		string $location,
		string $elementAction,
		array $elements,
		callable $identityGetter,
	): void
	{
		$duplicateElements = array_keys(
			array_filter(
				array_count_values(
					array_map(
						$identityGetter,
						$elements,
					),
				),
				static fn ($count) => $count > 1,
			),
		);

		foreach ($duplicateElements as $duplicateElement) {
			$errors->addErrorMessage(
				sprintf(
					"%s can't %s '%s' multiple times",
					$location,
					$elementAction,
					$duplicateElement,
				),
			);
		}
	}



	public function validateInputType(
		Errors $errors,
		string $location,
		Types\Type $type,
	): void
	{
		$namedType = $type->getNamedType();
		$typeDefinition = $this->typeDefinitionRegistry->getItemOrNull($namedType);

		if ($typeDefinition === null) {
			$errors->addErrorMessage(
				sprintf(
					"%s has unknown type '%s'",
					$location,
					$namedType,
				),
			);

			return;
		}

		if ($typeDefinition->kind->isInputType() === false) {
			$errors->addErrorMessage(
				sprintf(
					"%s must have input type, but %s type '%s' given",
					$location,
					$typeDefinition->kind->format(),
					$type->format(),
				),
			);
		}
	}



	public function validateOutputType(
		Errors $errors,
		string $location,
		Types\Type $type,
	): void
	{
		$namedType = $type->getNamedType();
		$typeDefinition = $this->typeDefinitionRegistry->getItemOrNull($namedType);

		if ($typeDefinition === null) {
			$errors->addErrorMessage(
				sprintf(
					"%s has unknown type '%s'",
					$location,
					$namedType,
				),
			);

			return;
		}

		if ($typeDefinition->kind->isOutputType() === false) {
			$errors->addErrorMessage(
				sprintf(
					"%s must have output type, but %s type '%s' given",
					$location,
					$typeDefinition->kind->format(),
					$type->format(),
				),
			);
		}
	}



	/**
	 * @param Values\Value<mixed> $actualValue
	 */
	public function validateValue(
		Errors $errors,
		string $location,
		Types\Type $expectedType,
		Values\Value $actualValue,
	): void
	{
		match (true) {
			$expectedType instanceof Types\ListType => $this->validateListValue(
				$errors,
				$location,
				$expectedType,
				$actualValue,
			),
			$expectedType instanceof Types\NamedType => $this->validateNamedValue(
				$errors,
				$location,
				$expectedType,
				$actualValue,
			),
			$expectedType instanceof Types\NonNullType => $this->validateNonNullValue(
				$errors,
				$location,
				$expectedType,
				$actualValue,
			),
		};
	}



	/**
	 * @param Values\Value<mixed> $actualValue
	 */
	private function validateEnumValue(
		Errors $errors,
		string $location,
		Types\NamedType $expectedType,
		Values\Value $actualValue,
	): void
	{
		if (!$actualValue instanceof Values\EnumValue) {
			$errors->addErrorMessage(
				sprintf(
					"%s must be enum '%s', but %s value given",
					$location,
					$expectedType->format(),
					$actualValue->getLabel(),
				),
			);

			return;
		}

		/** @var TypeSystem\EnumTypeDefinition $enumTypeDefinition (Correctness of the expected type is already validated before) */
		$enumTypeDefinition = $this->typeDefinitionRegistry->getItem($expectedType->name);

		foreach ($enumTypeDefinition->enumValues as $enumValueDefinition) {
			if ($enumValueDefinition->name === $actualValue->value) {
				return;
			}
		}

		$errors->addErrorMessage(
			sprintf(
				"%s with enum type '%s' doesn't recognize value '%s'",
				$location,
				$enumTypeDefinition->name,
				$actualValue->value,
			),
		);
	}



	/**
	 * @param Values\Value<mixed> $actualValue
	 */
	private function validateInputObjectValue(
		Errors $errors,
		string $location,
		Types\NamedType $expectedType,
		Values\Value $actualValue,
	): void
	{
		if (!$actualValue instanceof Values\ObjectValue) {
			$errors->addErrorMessage(
				sprintf(
					"%s must be input object '%s', but %s value given",
					$location,
					$expectedType->format(),
					$actualValue->getLabel(),
				),
			);

			return;
		}

		/** @var TypeSystem\InputObjectTypeDefinition $inputTypeDefinition (Correctness of the expected type is already validated before) */
		$inputTypeDefinition = $this->typeDefinitionRegistry->getItem($expectedType->name);

		$supportedFields = array_map(
			static fn ($field) => $field->name,
			$inputTypeDefinition->fields,
		);

		foreach ($actualValue->fields as $inputField) {
			if (in_array($inputField->name, $supportedFields, true) === false) {
				$errors->addErrorMessage(
					sprintf(
						"%s with input object type '%s' doesn't recognize field '%s'",
						$location,
						$inputTypeDefinition->name,
						$inputField->name,
					),
				);
			}
		}

		$duplicateFields = array_intersect(
			$actualValue->listDuplicateFields(),
			$supportedFields,
		);

		if ($duplicateFields !== []) {
			foreach ($duplicateFields as $duplicateField) {
				$errors->addErrorMessage(
					sprintf(
						"%s with input object type can't define field '%s' multiple times",
						$location,
						$duplicateField,
					),
				);
			}
		}

		foreach ($inputTypeDefinition->fields as $inputField) {
			$this->validateValue(
				$errors,
				"Value of field '{$inputField->name}' of " . lcfirst($location),
				$inputField->type,
				$actualValue->getFieldValue($inputField->name),
			);
		}
	}



	/**
	 * @param Values\Value<mixed> $actualValue
	 */
	private function validateListValue(
		Errors $errors,
		string $location,
		Types\ListType $expectedType,
		Values\Value $actualValue,
	): void
	{
		if (!$actualValue instanceof Values\ListValue) {
			$errors->addErrorMessage(
				sprintf(
					'%s must be %s, but %s value given',
					$location,
					$expectedType->format(),
					$actualValue->getLabel(),
				),
			);

			return;
		}

		$itemType = $expectedType->getWrappedType();

		foreach ($actualValue->items as $itemValue) {
			$this->validateValue(
				$errors,
				'X' . $location,
				$itemType,
				$itemValue,
			);
		}
	}



	/**
	 * @param Values\Value<mixed> $actualValue
	 */
	private function validateNamedValue(
		Errors $errors,
		string $location,
		Types\NamedType $expectedType,
		Values\Value $actualValue,
	): void
	{
		if ($actualValue instanceof Values\NullValue) {
			return;
		}

		/** @var TypeSystem\TypeKind::Enum|TypeSystem\TypeKind::InputObject|TypeSystem\TypeKind::Scalar $typeKind */
		$typeKind = $this->typeDefinitionRegistry->getItem($expectedType->name)->kind;

		match ($typeKind) {
			TypeSystem\TypeKind::Enum => $this->validateEnumValue(
				$errors,
				$location,
				$expectedType,
				$actualValue,
			),
			TypeSystem\TypeKind::InputObject => $this->validateInputObjectValue(
				$errors,
				$location,
				$expectedType,
				$actualValue,
			),
			TypeSystem\TypeKind::Scalar => $this->validateScalarValue(
				$errors,
				$location,
				$expectedType,
				$actualValue,
			),
		};
	}



	/**
	 * @param Values\Value<mixed> $actualValue
	 */
	private function validateNonNullValue(
		Errors $errors,
		string $location,
		Types\NonNullType $expectedType,
		Values\Value $actualValue,
	): void
	{
		if ($actualValue instanceof Values\NullValue) {
			$errors->addErrorMessage(
				sprintf(
					'%s must be %s, but %s value given',
					$location,
					$expectedType->format(),
					$actualValue->getLabel(),
				),
			);

			return;
		}

		$this->validateValue(
			$errors,
			$location,
			$expectedType->getWrappedType(),
			$actualValue,
		);
	}



	/**
	 * @param Values\Value<mixed> $actualValue
	 */
	private function validateScalarValue(
		Errors $errors,
		string $location,
		Types\NamedType $expectedType,
		Values\Value $actualValue,
	): void
	{
		/** @var TypeSystem\ScalarTypeDefinition $scalarTypeDefinition (Correctness of the expected type is already validated before) */
		$scalarTypeDefinition = $this->typeDefinitionRegistry->getItem($expectedType->name);

		try {
			$this->scalarImplementationRegistry->getItem($scalarTypeDefinition->name)->parseLiteralValue($actualValue);
		} catch (Exceptions\CannotParseScalarLiteralValueException $e) {
			$errors->addErrorMessage(
				sprintf(
					'%s %s',
					$location,
					$e->getMessage() !== ''
						? 'is invalid: ' . $e->getMessage()
						: sprintf("must be scalar '%s', but %s value given", $scalarTypeDefinition->name, $actualValue->getLabel()),
				),
			);
		}
	}



	/**
	 * @param ?Values\Value<mixed> $locationDefaultValue
	 * @param ?Values\Value<mixed> $variableDefaultValue
	 */
	private function validateVariableArgument(
		?Values\Value $locationDefaultValue,
		Types\Type $locationType,
		?Values\Value $variableDefaultValue,
		Types\Type $variableType,
	): bool
	{
		if (
			$locationType instanceof Types\NonNullType
			&& !$variableType instanceof Types\NonNullType
		) {
			$hasNonNullVariableDefaultValue = (
				$variableDefaultValue !== null
				&& !$variableDefaultValue instanceof Values\NullValue
			);

			if ($hasNonNullVariableDefaultValue === false && $locationDefaultValue === null) {
				return false;
			}

			return Spec::areTypesCompatible(
				variableType: $variableType,
				locationType: $locationType->getWrappedType(),
			);
		}

		return Spec::areTypesCompatible(
			variableType: $variableType,
			locationType: $locationType,
		);
	}



	public function validateFieldCovariance(
		TypeSystem\FieldDefinition $objectFieldDefinition,
		TypeSystem\FieldDefinition $interfaceFieldDefinition,
	): bool
	{
		return Spec::isValidImplementationFieldType(
			$this->typeDefinitionRegistry,
			$objectFieldDefinition->type,
			$interfaceFieldDefinition->type,
		);
	}

}
