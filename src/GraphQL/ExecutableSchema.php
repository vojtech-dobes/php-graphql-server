<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL;

use BackedEnum;
use Generator;
use Throwable;


final class ExecutableSchema
{

	/**
	 * @param ContextFactory<mixed> $contextFactory
	 */
	public function __construct(
		private readonly AbstractTypeResolverProvider $abstractTypeResolverProvider,
		private readonly ContextFactory $contextFactory,
		private readonly bool $enableIntrospection,
		private readonly ErrorHandler $errorHandler,
		private readonly FieldResolverProvider $fieldResolverProvider,
		public readonly TypeSystem\Schema $schema,
	) {}



	/**
	 * @throws Exceptions\InvalidExecutableSchemaException
	 */
	public function validate(): void
	{
		$errors = new Errors();

		$fieldResolverProvider = $this->createCompleteFieldResolverProvider();
		$queryType = $this->schema->rootOperationTypes[OperationType::Query->value];

		$knownFieldNames = [];

		foreach ($this->schema->getTypeDefinitions() as $typeDefinition) {
			if ($typeDefinition instanceof TypeSystem\ObjectTypeDefinition) {
				foreach ($typeDefinition->fields as $fieldDefinition) {
					$fieldName = "{$typeDefinition->name}.{$fieldDefinition->name}";

					$knownFieldNames[] = $fieldName;

					if ($fieldDefinition->type->getNamedType() === $queryType) {
						if ($fieldResolverProvider->hasFieldResolver($fieldName)) {
							$errors->addErrorMessage(
								sprintf(
									"Field '%s' resolving to root type can't have a resolver",
									$fieldName,
								),
							);
						}

						continue;
					}

					if ($fieldResolverProvider->hasFieldResolver($fieldName) === false) {
						$errors->addErrorMessage(
							sprintf(
								"Field '%s' doesn't have a resolver",
								$fieldName,
							),
						);
					}
				}
			} elseif (
				$typeDefinition instanceof TypeSystem\InterfaceTypeDefinition
				|| $typeDefinition instanceof TypeSystem\UnionTypeDefinition
			) {
				$requiresResolver = array_any(
					$this->schema->getTypeDefinitions(),
					static fn ($aTypeDefinition) => (
						$aTypeDefinition instanceof TypeSystem\ObjectTypeDefinition
						&& array_any(
							$aTypeDefinition->fields,
							static fn ($fieldDefinition) => $fieldDefinition->type->getNamedType() === $typeDefinition->name,
						)
					),
				);

				if ($requiresResolver && $this->abstractTypeResolverProvider->hasAbstractTypeResolver($typeDefinition->name) === false) {
					$errors->addErrorMessage(
						sprintf(
							"Abstract %s type '%s' doesn't have a resolver",
							$typeDefinition->kind->format(),
							$typeDefinition->name,
						),
					);
				}
			}
		}

		foreach ($this->abstractTypeResolverProvider->listSupportedTypeNames() as $typeName) {
			if ($this->schema->getTypeDefinitionOrNull($typeName) === null) {
				$errors->addErrorMessage(
					sprintf(
						"Abstract type '%s' has resolver but doesn't exist in schema",
						$typeName,
					),
				);
			}
		}

		if ($errors->errors !== []) {
			throw new Exceptions\InvalidExecutableSchemaException($errors->errors);
		}

		foreach ($fieldResolverProvider->listSupportedFieldNames() as $fieldName) {
			if (in_array($fieldName, $knownFieldNames, true) === false) {
				$errors->addErrorMessage(
					sprintf(
						"Field '%s' has resolver but doesn't exist in schema",
						$fieldName,
					),
				);
			}
		}

		if ($errors->errors !== []) {
			throw new Exceptions\InvalidExecutableSchemaException($errors->errors);
		}
	}



	/**
	 * @param list<Executable\VariableDefinition> $variableDefinitions
	 * @param array<string, mixed> $variableValues
	 * @throws Exceptions\CannotResolveVariableRuntimeValuesException
	 */
	public function createOperationExecution(
		Executable\Document $document,
		array $variableDefinitions,
		array $variableValues,
		?bool $enableIntrospection,
	): Execution\OperationExecution
	{
		return new Execution\OperationExecution(
			abstractTypeResolverProvider: $this->abstractTypeResolverProvider,
			context: $this->contextFactory->createContext(),
			document: $document,
			enableIntrospection: $enableIntrospection ?? $this->enableIntrospection,
			errorHandler: $this->errorHandler,
			fieldResolverProvider: $this->createCompleteFieldResolverProvider(),
			schema: $this->schema,
			variables: $this->createVariables(
				$variableDefinitions,
				$variableValues,
			),
		);
	}



	private function createCompleteFieldResolverProvider(): CombinedFieldResolverProvider
	{
		return new CombinedFieldResolverProvider([
			$this->fieldResolverProvider,
			new Introspection\IntrospectionFieldResolverProvider($this->schema),
		]);
	}



	/**
	 * @param list<Executable\VariableDefinition> $variableDefinitions
	 * @param array<string, mixed> $variableValues
	 * @return array<string, mixed>
	 * @throws Exceptions\CannotResolveVariableRuntimeValuesException
	 */
	private function createVariables(
		array $variableDefinitions,
		array $variableValues,
	): array
	{
		$errors = new Errors();
		$result = [];

		foreach ($variableDefinitions as $variableDefinition) {
			try {
				$result[$variableDefinition->name] = $this->resolveRuntimeValue(
					$variableDefinition->type,
					$variableValues[$variableDefinition->name] ?? null,
				);
			} catch (Execution\CannotResolveVariableRuntimeValueException) {
				$errors->addErrorMessage(
					sprintf(
						"Value for variable '%s' must conform to %s",
						$variableDefinition->name,
						$variableDefinition->type->format(),
					),
				);

				continue;
			}

			if (
				$result[$variableDefinition->name] === null
				&& $variableDefinition->defaultValue !== null
			) {
				$result[$variableDefinition->name] = $variableDefinition->getDefaultRuntimeValue();
			}
		}

		if ($errors->errors !== []) {
			throw new Exceptions\CannotResolveVariableRuntimeValuesException($errors->errors);
		}

		return $result;
	}



	/**
	 * @throws Execution\CannotResolveVariableRuntimeValueException
	 */
	private function resolveRuntimeValue(
		Types\Type $type,
		mixed $value,
	): mixed
	{
		if ($type instanceof Types\NonNullType) {
			if ($value === null) {
				throw new Execution\CannotResolveVariableRuntimeValueException();
			}

			return $this->resolveRuntimeValue(
				$type->getWrappedType(),
				$value,
			);
		}

		if ($value === null) {
			return null;
		}

		if ($type instanceof Types\ListType) {
			if (is_iterable($value) === false) {
				throw new Execution\CannotResolveVariableRuntimeValueException();
			}

			$result = [];

			foreach ($value as $item) {
				$result[] = $this->resolveRuntimeValue(
					$type->getWrappedType(),
					$item,
				);
			}

			return $result;
		}

		$typeDefinition = $this->schema->getTypeDefinition($type->name);

		if ($typeDefinition instanceof TypeSystem\EnumTypeDefinition) {
			$enumClass = $this->schema->getEnumClass($typeDefinition->name);

			if ($enumClass !== null) {
				return call_user_func([$enumClass, 'from'], $value);
			}

			return $value;
		}

		if ($typeDefinition instanceof TypeSystem\InputObjectTypeDefinition) {
			$result = [];

			if (is_array($value) === false) {
				throw new Execution\CannotResolveVariableRuntimeValueException();
			}

			foreach ($typeDefinition->fields as $inputValueDefinition) {
				if (array_key_exists($inputValueDefinition->name, $value)) {
					$result[$inputValueDefinition->name] = $this->resolveRuntimeValue(
						$inputValueDefinition->type,
						$value[$inputValueDefinition->name],
					);
				} elseif ($inputValueDefinition->defaultValue !== null) {
					$result[$inputValueDefinition->name] = $inputValueDefinition->defaultValue->getRuntimeValue();
				}
			}

			return $result;
		}

		/** @var TypeSystem\ScalarTypeDefinition $typeDefinition */

		try {
			return $this->schema
				->scalarImplementationRegistry
				->getItem($typeDefinition->name)
				->parseRuntimeValue($value);
		} catch (Exceptions\CannotParseScalarRuntimeValueException $e) {
			throw new Execution\CannotResolveVariableRuntimeValueException('', 0, $e);
		}
	}

}
