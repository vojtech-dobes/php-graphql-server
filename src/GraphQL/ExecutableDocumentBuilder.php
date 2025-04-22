<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL;

use LogicException;
use Vojtechdobes\GrammarProcessing;


final class ExecutableDocumentBuilder
{

	private readonly Errors $errors;

	/** @var array<string, Executable\FragmentDefinition> */
	private $fragmentDefinitions = [];

	/** @var list<Executable\OperationDefinition> */
	private $operationDefinitions = [];

	/** @var list<TypeSystem\ObjectTypeDefinition> */
	private $operationRootTypeDefinitions = [];

	/** @var list<string> */
	private $usedFragmentDefinitions = [];

	private Validator $validator;



	public function __construct(
		private readonly TypeSystem\Schema $schema,
	)
	{
		$this->errors = new Errors();

		$this->validator = new Validator(
			directiveDefinitionRegistry: $this->schema->directiveDefinitionRegistry,
			scalarImplementationRegistry: $this->schema->scalarImplementationRegistry,
			typeDefinitionRegistry: $this->schema->typeDefinitionRegistry,
		);
	}



	public function addOperationDefinition(Executable\OperationDefinition $operationDefinition): void
	{
		$this->operationDefinitions[] = $operationDefinition;
	}



	public function addFragmentDefinition(Executable\FragmentDefinition $fragmentDefinition): void
	{
		if (array_key_exists($fragmentDefinition->name, $this->fragmentDefinitions)) {
			$this->errors->addErrorMessage(
				sprintf(
					"Fragment '%s' can't be defined multiple times",
					$fragmentDefinition->name,
				),
			);

			return;
		}

		$this->fragmentDefinitions[$fragmentDefinition->name] = $fragmentDefinition;
	}



	/**
	 * @throws Exceptions\InconsistentExecutableDocumentException
	 */
	public function buildExecutableDocument(): Executable\Document
	{
		foreach ($this->fragmentDefinitions as $fragmentDefinition) {
			$this->validateFragmentDefinition($fragmentDefinition);
		}

		if (
			count($this->operationDefinitions) > 1
			&& count(array_filter(
				$this->operationDefinitions,
				static fn ($operationDefinition) => $operationDefinition->name === null,
			)) > 0
		) {
			$this->errors->addErrorMessage(
				"In presence of multiple operations, anonymous operations aren't allowed",
			);
		}

		foreach ($this->operationDefinitions as $operationDefinition) {
			$this->validateOperationDefinition($operationDefinition);
		}

		if ($this->operationDefinitions === []) {
			$this->errors->addErrorMessage(
				"Document doesn't contain any executable operations",
			);
		} else {
			foreach ($this->fragmentDefinitions as $fragmentDefinition) {
				if (in_array($fragmentDefinition->name, $this->usedFragmentDefinitions, true) === false) {
					$this->errors->addErrorMessage(
						sprintf(
							"Fragment '%s' isn't used anywhere",
							$fragmentDefinition->name,
						),
					);
				}
			}
		}

		if ($this->errors->errors !== []) {
			throw new Exceptions\InconsistentExecutableDocumentException(
				array_map(
					static fn ($error) => $error->message,
					$this->errors->errors,
				),
			);
		}

		return new Executable\Document(
			fragmentDefinitions: $this->fragmentDefinitions,
			operationDefinitions: $this->operationDefinitions,
			operationRootTypeDefinitions: $this->operationRootTypeDefinitions,
		);
	}



	private function validateFragmentDefinition(
		Executable\FragmentDefinition $fragmentDefinition,
	): void
	{
		$typeDefinition = $this->schema->getTypeDefinitionOrNull($fragmentDefinition->onType->name);

		if ($typeDefinition === null) {
			$this->errors->addErrorMessage(
				sprintf(
					"Type condition of fragment '%s' references unknown type '%s'",
					$fragmentDefinition->name,
					$fragmentDefinition->onType->name,
				),
			);

			return;
		}

		if (!$typeDefinition instanceof TypeSystem\TypeWithFieldSelection) {
			$this->errors->addErrorMessage(
				sprintf(
					"Type condition of fragment '%s' must be interface, object or union, but %s type '%s' given",
					$fragmentDefinition->name,
					$typeDefinition->kind->format(),
					$typeDefinition->name,
				),
			);

			return;
		}

		$this->validator->validateDirectives(
			$this->errors,
			[],
			"Fragment '$fragmentDefinition->name'",
			Spec\ExecutableDirectiveLocation::FragmentDefinition,
			$fragmentDefinition->directives,
		);
	}



	private function validateOperationDefinition(
		Executable\OperationDefinition $operationDefinition,
	): void
	{
		$rootOperationTypeDefinition = $this->schema->getRootOperationTypeDefinition($operationDefinition->type);

		if ($rootOperationTypeDefinition === null) {
			$this->errors->addErrorMessage(
				sprintf(
					"Schema doesn't support %s operation",
					lcfirst($operationDefinition->type->format()),
				),
			);

			return;
		}

		$this->operationRootTypeDefinitions[] = $rootOperationTypeDefinition;

		$operationDefinitionLocation = $operationDefinition->format();

		Validator::validateDuplicates(
			$this->errors,
			$operationDefinitionLocation,
			'define variable',
			$operationDefinition->variableDefinitions,
			static fn ($variableDefinition) => $variableDefinition->name,
		);

		$operationVariableDefinitions = [];

		foreach ($operationDefinition->variableDefinitions as $variableDefinition) {
			$variableDefinitionLocation = sprintf(
				"Variable '%s' of %s",
				$variableDefinition->name,
				$operationDefinitionLocation,
			);

			$this->validator->validateInputType(
				$this->errors,
				$variableDefinitionLocation,
				$variableDefinition->type,
			);

			if ($variableDefinition->defaultValue !== null) {
				if ($variableDefinition->defaultValue instanceof Executable\Variable) {
					$this->errors->addErrorMessage(
						sprintf(
							"%s can't have another variable as it's default value",
							$variableDefinitionLocation,
						),
					);

					continue;
				}

				$this->validator->validateValue(
					$this->errors,
					sprintf("Default value of %s", lcfirst($variableDefinitionLocation)),
					$variableDefinition->type,
					$variableDefinition->defaultValue,
				);
			}

			$operationVariableDefinitions[$variableDefinition->name] = $variableDefinition;
		}

		$this->validator->validateDirectives(
			$this->errors,
			$operationVariableDefinitions,
			$operationDefinitionLocation,
			$operationDefinition->type->getExecutableDirectiveLocation(),
			$operationDefinition->directives,
		);

		$this->validateSelectionSet(
			$operationVariableDefinitions,
			$operationDefinitionLocation,
			$rootOperationTypeDefinition,
			$operationDefinition->selectionSet,
		);
	}



	/**
	 * @param array<string, Executable\VariableDefinition> $variableDefinitions
	 * @param list<Executable\Selection> $selectionSet
	 */
	private function validateSelectionSet(
		array $variableDefinitions,
		string $location,
		TypeSystem\TypeWithFieldSelection $typeDefinition,
		array $selectionSet,
	): void
	{
		if ($selectionSet === []) {
			return;
		}

		foreach ($selectionSet as $selection) {
			match (true) {
				$selection instanceof Executable\Field => $this->validateField(
					$variableDefinitions,
					"Field '{$selection->name}'",
					$typeDefinition,
					$selection,
				),
				$selection instanceof Executable\FragmentSpread => $this->validateFragmentSpread(
					$variableDefinitions,
					$location,
					$typeDefinition,
					$selection,
				),
				$selection instanceof Executable\InlineFragment => $this->validateInlineFragment(
					$variableDefinitions,
					$location,
					$typeDefinition,
					$selection,
				),
			};
		}
	}



	/**
	 * @param array<string, Executable\VariableDefinition> $variableDefinitions
	 */
	private function validateField(
		array $variableDefinitions,
		string $location,
		TypeSystem\TypeWithFieldSelection $typeDefinition,
		Executable\Field $field,
	): void
	{
		if ($field->name === '__typename') {
			return;
		}

		if ($typeDefinition instanceof TypeSystem\UnionTypeDefinition) {
			$this->errors->addErrorMessage(
				sprintf(
					"Only fragments and __typename are allowed in selection set on union type '%s'",
					$typeDefinition->name,
				),
			);

			return;
		}

		/** @var TypeSystem\InterfaceTypeDefinition|TypeSystem\ObjectTypeDefinition $typeDefinition */

		$fieldDefinition = match ($field->name) {
			'__schema' => $this->getIntrospectionSchemaField($typeDefinition),
			'__type' => $this->getIntrospectionTypeField($typeDefinition),
			default => $typeDefinition->fieldsByName[$field->name] ?? null,
		};

		if ($fieldDefinition === null) {
			$this->errors->addErrorMessage(
				sprintf(
					"Selection set on type '%s' references unknown field '%s'",
					$typeDefinition->name,
					$field->name,
				),
			);

			return;
		}

		$this->validator->validateArguments(
			$this->errors,
			$variableDefinitions,
			$location,
			$fieldDefinition->argumentDefinitions,
			$field->arguments,
		);

		$this->validator->validateDirectives(
			$this->errors,
			$variableDefinitions,
			$location,
			Spec\ExecutableDirectiveLocation::Field,
			$field->directives,
		);

		$fieldTypeDefinition = $this->schema->getTypeDefinition($fieldDefinition->type->getNamedType());

		if ($fieldTypeDefinition instanceof TypeSystem\TypeWithFieldSelection) {
			if ($field->selectionSet !== null) {
				$this->validateSelectionSet(
					$variableDefinitions,
					$location,
					$fieldTypeDefinition,
					$field->selectionSet,
				);
			} else {
				$this->errors->addErrorMessage(
					sprintf(
						"Object field '%s.%s' has %s type '%s' which requires selecting subfields",
						$typeDefinition->name,
						$field->name,
						$fieldTypeDefinition->kind->format(),
						$fieldTypeDefinition->name,
					),
				);
			}
		} elseif ($field->selectionSet !== null) {
			$this->errors->addErrorMessage(
				sprintf(
					"Object field '%s.%s' has %s type '%s' which doesn't support selecting subfields",
					$typeDefinition->name,
					$field->name,
					$fieldTypeDefinition->kind->format(),
					$fieldTypeDefinition->name,
				),
			);
		}
	}



	private function getIntrospectionSchemaField(
		TypeSystem\TypeWithFieldSelection $typeDefinition,
	): ?TypeSystem\FieldDefinition
	{
		if ($this->schema->getRootOperationTypeDefinition(OperationType::Query) !== $typeDefinition) {
			return null;
		}

		return new TypeSystem\FieldDefinition(
			argumentDefinitions: [],
			description: null,
			directives: [],
			name: '__schema',
			type: new Types\NonNullType(
				new Types\NamedType('__Schema'),
			),
		);
	}



	private function getIntrospectionTypeField(
		TypeSystem\TypeWithFieldSelection $typeDefinition,
	): ?TypeSystem\FieldDefinition
	{
		if ($this->schema->getRootOperationTypeDefinition(OperationType::Query) !== $typeDefinition) {
			return null;
		}

		return new TypeSystem\FieldDefinition(
			argumentDefinitions: [
				new TypeSystem\InputValueDefinition(
					defaultValue: null,
					description: null,
					directives: [],
					name: 'name',
					type: new Types\NonNullType(
						new Types\NamedType('String'),
					),
				),
			],
			description: null,
			directives: [],
			name: '__type',
			type: new Types\NamedType('__Type'),
		);
	}



	/**
	 * @param array<string, Executable\VariableDefinition> $variableDefinitions
	 */
	private function validateFragmentSpread(
		array $variableDefinitions,
		string $location,
		TypeSystem\TypeWithFieldSelection $typeDefinition,
		Executable\FragmentSpread $fragmentSpread,
	): void
	{
		$fragmentDefinition = $this->fragmentDefinitions[$fragmentSpread->fragmentName] ?? null;

		if ($fragmentDefinition === null) {
			$this->errors->addErrorMessage(
				sprintf(
					"Fragment '%s' is unknown",
					$fragmentSpread->fragmentName,
				),
			);

			return;
		}

		$this->usedFragmentDefinitions[] = $fragmentDefinition->name;

		/** @var TypeSystem\TypeWithFieldSelection|null $fragmentTypeDefinition */
		$fragmentTypeDefinition = $this->schema->getTypeDefinitionOrNull($fragmentDefinition->onType->name);

		if ($fragmentTypeDefinition === null) {
			return;
		}

		if ($typeDefinition instanceof TypeSystem\InterfaceTypeDefinition) {
			$isCompatible = match (true) {
				$fragmentTypeDefinition instanceof TypeSystem\InterfaceTypeDefinition,
				$fragmentTypeDefinition instanceof TypeSystem\ObjectTypeDefinition => (
					$typeDefinition->name === $fragmentTypeDefinition->name
					|| in_array($typeDefinition->name, $fragmentTypeDefinition->implementedInterfaces, true)
				),
				$fragmentTypeDefinition instanceof TypeSystem\UnionTypeDefinition => $fragmentTypeDefinition->name === $typeDefinition->name,
			};

			if ($isCompatible === false) {
				$this->errors->addErrorMessage(
					sprintf(
						"Type condition of fragment '%s' spread on interface type '%s' must be on a compatible type, but %s type '%s' given",
						$fragmentDefinition->name,
						$typeDefinition->name,
						$fragmentTypeDefinition->kind->format(),
						$fragmentTypeDefinition->name,
					),
				);

				return;
			}
		} elseif ($typeDefinition instanceof TypeSystem\UnionTypeDefinition) {
			if (in_array($fragmentTypeDefinition->name, $typeDefinition->possibleTypes, true) === false) {
				$this->errors->addErrorMessage(
					sprintf(
						"Type condition of fragment '%s' spread on union type '%s' must be on its member type, but %s type '%s' given",
						$fragmentDefinition->name,
						$typeDefinition->name,
						$fragmentTypeDefinition->kind->format(),
						$fragmentTypeDefinition->name,
					),
				);

				return;
			}
		}

		$this->validator->validateDirectives(
			$this->errors,
			$variableDefinitions,
			"Spread of fragment '{$fragmentDefinition->name}'",
			Spec\ExecutableDirectiveLocation::FragmentSpread,
			$fragmentSpread->directives,
		);

		$this->validateSelectionSet(
			$variableDefinitions,
			"Fragment '{$fragmentDefinition->name}'",
			$typeDefinition,
			$fragmentDefinition->selectionSet,
		);
	}



	/**
	 * @param array<string, Executable\VariableDefinition> $variableDefinitions
	 */
	private function validateInlineFragment(
		array $variableDefinitions,
		string $location,
		TypeSystem\TypeWithFieldSelection $typeDefinition,
		Executable\InlineFragment $inlineFragment,
	): void
	{
		if ($inlineFragment->onType !== null) {
			$fragmentTypeDefinition = $this->schema->getTypeDefinitionOrNull($inlineFragment->onType->name);

			if ($fragmentTypeDefinition === null) {
				$this->errors->addErrorMessage(
					sprintf(
						"Type condition of inline fragment references unknown type '%s'",
						$inlineFragment->onType->name,
					),
				);

				return;
			}

			/** @var TypeSystem\TypeWithFieldSelection $fragmentTypeDefinition (This is verified by being already in selection) */

			if ($typeDefinition instanceof TypeSystem\InterfaceTypeDefinition) {
				if (
					!$fragmentTypeDefinition instanceof TypeSystem\ObjectTypeDefinition
					|| in_array($typeDefinition->name, $fragmentTypeDefinition->implementedInterfaces, true) === false
				) {
					$this->errors->addErrorMessage(
						sprintf(
							"Type condition of inline fragment on interface type '%s' must be on object type implementing it, but %s type '%s' given",
							$typeDefinition->name,
							$fragmentTypeDefinition->kind->format(),
							$fragmentTypeDefinition->name,
						),
					);

					return;
				}
			} elseif ($typeDefinition instanceof TypeSystem\UnionTypeDefinition) {
				if (in_array($fragmentTypeDefinition->name, $typeDefinition->possibleTypes, true) === false) {
					$this->errors->addErrorMessage(
						sprintf(
							"Type condition of inline fragment on union type '%s' must be on its member type, but %s type '%s' given",
							$typeDefinition->name,
							$fragmentTypeDefinition->kind->format(),
							$fragmentTypeDefinition->name,
						),
					);

					return;
				}

				/** @var TypeSystem\ObjectTypeDefinition $fragmentTypeDefinition (This is verified by being among Union possible types which must be Object) */
			}
		} else {
			$fragmentTypeDefinition = $typeDefinition;
		}

		$this->validator->validateDirectives(
			$this->errors,
			$variableDefinitions,
			sprintf("Inline fragment on %s", lcfirst($location)),
			Spec\ExecutableDirectiveLocation::InlineFragment,
			$inlineFragment->directives,
		);

		$this->validateSelectionSet(
			$variableDefinitions,
			$location,
			$fragmentTypeDefinition,
			$inlineFragment->selectionSet,
		);
	}

}
