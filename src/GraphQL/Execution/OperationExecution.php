<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\Execution;

use BackedEnum;
use Generator;
use GuzzleHttp;
use LogicException;
use Throwable;
use Vojtechdobes\GraphQL;


final class OperationExecution
{

	/** @var GraphQL\FieldResolver<null, covariant GraphQL\TypeSystem\Schema|null> */
	private readonly GraphQL\FieldResolver $introspectionSchemaResolver;

	/** @var GraphQL\FieldResolver<null, covariant GraphQL\Types\NamedType|null, covariant array{name: string}|array{}> */
	private readonly GraphQL\FieldResolver $introspectionTypeResolver;

	private readonly string $queryType;



	/**
	 * @param array<string, mixed> $variables
	 */
	public function __construct(
		private readonly GraphQL\AbstractTypeResolverProvider $abstractTypeResolverProvider,
		private readonly mixed $context,
		private readonly GraphQL\Executable\Document $document,
		private readonly GraphQL\ErrorHandler $errorHandler,
		private readonly GraphQL\FieldResolverProvider $fieldResolverProvider,
		private readonly GraphQL\TypeSystem\Schema $schema,
		private readonly array $variables,
		bool $enableIntrospection,
	)
	{
		if ($enableIntrospection) {
			$this->introspectionSchemaResolver = new GraphQL\Introspection\SchemaFieldResolver($schema);
			$this->introspectionTypeResolver = new GraphQL\Introspection\TypeFieldResolver($schema);
		} else {
			$resolver = new GraphQL\CallbackFieldResolver(static fn () => null);

			$this->introspectionSchemaResolver = $resolver;
			$this->introspectionTypeResolver = $resolver;
		}

		$this->queryType = $schema->rootOperationTypes[GraphQL\OperationType::Query->value];
	}



	/**
	 * @param list<GraphQL\Executable\Selection> $selectionSet
	 */
	public function executeSelectionSet(
		GraphQL\TypeSystem\ObjectTypeDefinition $objectType,
		mixed $objectValue,
		array $selectionSet,
	): GuzzleHttp\Promise\PromiseInterface
	{
		$fields = $this->collectFields(
			$objectType,
			$selectionSet,
			[],
		);

		$areAllFieldsNotNullable = true;
		$executionFields = [];
		$resolvedValues = [];

		foreach ($fields as $fieldResponseKey => $fieldSelection) {
			$field = $fieldSelection[0];

			if ($field->name === '__typename') {
				$resolvedValues[$fieldResponseKey] = new ScalarFieldValue($objectType->name);

				continue;
			}

			$executionField = $this->createExecutionField(
				$objectType,
				$field,
				$fieldSelection,
			);

			$executionFields[$fieldResponseKey] = $executionField;

			if ($field->name === '__schema') {
				$resolvedValues[$fieldResponseKey] = $this->completeValue(
					$field,
					$executionField,
					new GraphQL\Types\NonNullType(
						new GraphQL\Types\NamedType('__Schema'),
					),
					$this->executeField(
						$this->introspectionSchemaResolver,
						null,
						$field,
						$executionField,
					),
				);

				continue;
			}

			if ($field->name === '__type') {
				$resolvedValues[$fieldResponseKey] = $this->completeValue(
					$field,
					$executionField,
					new GraphQL\Types\NamedType('__Type'),
					$this->executeField(
						$this->introspectionTypeResolver,
						null,
						$field,
						$executionField,
					),
				);

				$areAllFieldsNotNullable = false;

				continue;
			}

			$field = $fields[$fieldResponseKey][0];
			$fieldType = $objectType->fieldsByName[$field->name]->type;

			$resolvedValue = $this->executeField(
				$fieldType->getNamedType() === $this->queryType
					? new GraphQL\CallbackFieldResolver(static fn () => [])
					: $this->fieldResolverProvider->getFieldResolver("{$objectType->name}.{$field->name}") ?? throw new LogicException("This can't happen"),
				$objectValue,
				$field,
				$executionField,
			);

			if ($resolvedValue instanceof FieldValue) {
				$resolvedValues[$fieldResponseKey] = $resolvedValue;
			} else {
				$promise = match (true) {
					is_object($resolvedValue) && method_exists($resolvedValue, 'then') => $resolvedValue,
					$resolvedValue instanceof GraphQL\Deferred => $resolvedValue->createPromise(),
					default => null,
				};

				if ($promise !== null) {
					$promise = $promise->otherwise(
						function (Throwable $reason) use ($executionField): ErrorFieldValue {
							if ($reason instanceof GraphQL\Exceptions\FailedToResolveFieldException) {
								return new ErrorFieldValue(
									new GraphQL\Error(
										$reason->getMessage(),
										null,
										$reason->extensions,
									),
								);
							} else {
								$this->errorHandler->handleFieldResolverError($reason, $executionField);

								return new ErrorFieldValue(
									new GraphQL\Error("Field failed to resolve"),
								);
							}
						},
					);

					$resolvedValues[$fieldResponseKey] = $promise->then(
						fn ($settledValue) => $settledValue instanceof FieldValue ? $settledValue : $this->completeValue(
							$field,
							$executionFields[$fieldResponseKey],
							$fieldType,
							$settledValue,
						),
					);
				} else {
					$resolvedValues[$fieldResponseKey] = $this->completeValue(
						$field,
						$executionFields[$fieldResponseKey],
						$fieldType,
						$resolvedValue,
					);
				}
			}

			if ($areAllFieldsNotNullable && !$fieldType instanceof GraphQL\Types\NonNullType) {
				$areAllFieldsNotNullable = false;
			}
		}

		$fieldResponseKeys = array_keys($resolvedValues);

		return GuzzleHttp\Promise\Utils::all($resolvedValues)->then(
			static fn ($settledValues) => new ObjectFieldValue(
				$areAllFieldsNotNullable,
				array_combine(
					$fieldResponseKeys,
					array_map(
						static fn ($fieldResponseKey) => $settledValues[$fieldResponseKey],
						$fieldResponseKeys,
					),
				),
			),
		);
	}



	/**
	 * @param list<GraphQL\Executable\Selection> $selectionSet
	 * @param list<string> $visitedFragments
	 * @return array<string, non-empty-list<GraphQL\Executable\Field>>
	 */
	private function collectFields(
		GraphQL\TypeSystem\ObjectTypeDefinition $objectType,
		array $selectionSet,
		array $visitedFragments,
	): array
	{
		$result = [];

		foreach ($selectionSet as $selection) {
			foreach ($selection->directives as $directive) {
				if ($directive->name === 'include') {
					foreach ($directive->arguments as $argument) {
						if ($argument->name === 'if' && $this->resolveArgumentValue($argument) === false) {
							continue 3;
						}
					}
				}

				if ($directive->name === 'skip') {
					foreach ($directive->arguments as $argument) {
						if ($argument->name === 'if' && $this->resolveArgumentValue($argument) === true) {
							continue 3;
						}
					}
				}
			}

			$fieldsGenerator = match (true) {
				$selection instanceof GraphQL\Executable\Field => $this->collectFromField($selection),
				$selection instanceof GraphQL\Executable\FragmentSpread => $this->collectFromFragmentSpread(
					$objectType,
					$selection,
					$visitedFragments,
				),
				$selection instanceof GraphQL\Executable\InlineFragment => $this->collectFromInlineFragment(
					$objectType,
					$selection,
					$visitedFragments,
				),
			};

			foreach ($fieldsGenerator as $responseKey => $responseKeySelections) {
				$result[$responseKey] = [
					...$result[$responseKey] ?? [],
					...$responseKeySelections,
				];
			}
		}

		return $result;
	}



	/**
	 * @return Generator<string, non-empty-list<GraphQL\Executable\Field>, void, void>
	 */
	private function collectFromField(GraphQL\Executable\Field $field): Generator
	{
		yield $field->getResponseKey() => [$field];
	}



	/**
	 * @param list<string> $visitedFragments
	 * @return Generator<string, non-empty-list<GraphQL\Executable\Field>, void, void>
	 */
	private function collectFromFragmentSpread(
		GraphQL\TypeSystem\ObjectTypeDefinition $objectType,
		GraphQL\Executable\FragmentSpread $fragmentSpread,
		array $visitedFragments,
	): Generator
	{
		if (in_array($fragmentSpread->fragmentName, $visitedFragments, true)) {
			return;
		}

		$visitedFragments[] = $fragmentSpread->fragmentName;

		$fragmentDefinition = $this->document->fragmentDefinitions[$fragmentSpread->fragmentName];
		/** @var GraphQL\TypeSystem\TypeWithFieldSelection $fragmentType (This is already ensured during Validation of the Document) */
		$fragmentType = $this->schema->getTypeDefinition($fragmentDefinition->onType->name);

		if ($objectType->doesFragmentTypeApply($fragmentType) === false) {
			return;
		}

		yield from $this->collectFields(
			$objectType,
			$fragmentDefinition->selectionSet,
			$visitedFragments,
		);
	}



	/**
	 * @param list<string> $visitedFragments
	 * @return Generator<string, non-empty-list<GraphQL\Executable\Field>, void, void>
	 */
	private function collectFromInlineFragment(
		GraphQL\TypeSystem\ObjectTypeDefinition $objectType,
		GraphQL\Executable\InlineFragment $inlineFragment,
		array $visitedFragments,
	): Generator
	{
		if ($inlineFragment->onType !== null) {
			/** @var GraphQL\TypeSystem\TypeWithFieldSelection $fragmentType (This is already ensured during Validation of the Document) */
			$fragmentType = $this->schema->getTypeDefinition($inlineFragment->onType->name);

			if ($objectType->doesFragmentTypeApply($fragmentType) === false) {
				return;
			}
		}

		yield from $this->collectFields(
			$objectType,
			$inlineFragment->selectionSet,
			$visitedFragments,
		);
	}



	/**
	 * @param non-empty-list<GraphQL\Executable\Field> $fieldSelectionSets
	 * @return GraphQL\FieldSelection<array<string, mixed>, mixed>
	 */
	private function createExecutionField(
		GraphQL\TypeSystem\ObjectTypeDefinition $objectTypeDefinition,
		GraphQL\Executable\Field $field,
		array $fieldSelectionSets,
	): GraphQL\FieldSelection
	{
		$arguments = [];

		foreach ($field->arguments as $argument) {
			$arguments[$argument->name] = $this->resolveArgumentValue($argument);
		}

		if ($field->name === '__schema' || $field->name === '__type') {
			$argumentDefinitions = [];
		} else {
			$argumentDefinitions = $objectTypeDefinition->fieldsByName[$field->name]->argumentDefinitions;
		}

		foreach ($argumentDefinitions as $argumentDefinition) {
			if (isset($arguments[$argumentDefinition->name]) === false) {
				$arguments[$argumentDefinition->name] = $argumentDefinition->defaultValue?->getRuntimeValue();
			}
		}

		$mergedSelectionSet = [];

		foreach ($fieldSelectionSets as $fieldSelectionSet) {
			if ($fieldSelectionSet->selectionSet !== null) {
				$mergedSelectionSet[] = $fieldSelectionSet->selectionSet;
			}
		}

		return new GraphQL\FieldSelection(
			arguments: $arguments,
			context: $this->context,
			field: $field,
			selectionSet: array_merge(...$mergedSelectionSet),
		);
	}



	/**
	 * @param GraphQL\Executable\Argument<GraphQL\Values\Value<mixed>|GraphQL\Executable\Variable> $argument
	 */
	private function resolveArgumentValue(GraphQL\Executable\Argument $argument): mixed
	{
		return match (true) {
			$argument->value instanceof GraphQL\Executable\Variable => $this->variables[$argument->value->name],
			$argument->value instanceof GraphQL\Values\Value => $argument->value->getRuntimeValue(),
		};
	}



	/**
	 * @param GraphQL\FieldResolver<mixed, mixed, covariant array<string, mixed>, mixed> $fieldResolver
	 */
	private function executeField(
		GraphQL\FieldResolver $fieldResolver,
		mixed $objectValue,
		GraphQL\Executable\Field $field,
		GraphQL\FieldSelection $executionField,
	): mixed
	{
		try {
			$value = $fieldResolver->resolveField($objectValue, $executionField);
		} catch (GraphQL\Exceptions\FailedToResolveFieldException $e) {
			return new ErrorFieldValue(
				new GraphQL\Error(
					$e->getMessage(),
					null,
					$e->extensions,
				),
			);
		} catch (Throwable $e) {
			$this->errorHandler->handleFieldResolverError($e, $executionField);

			return new ErrorFieldValue(
				new GraphQL\Error("Field failed to resolve"),
			);
		}

		return $value;
	}



	/**
	 * @return FieldValue<mixed>|GuzzleHttp\Promise\PromiseInterface
	 */
	private function completeValue(
		GraphQL\Executable\Field $field,
		GraphQL\FieldSelection $executionField,
		GraphQL\Types\Type $fieldType,
		mixed $value,
	): FieldValue|GuzzleHttp\Promise\PromiseInterface
	{
		if ($fieldType instanceof GraphQL\Types\NonNullType) {
			$completedValue = $this->completeValue(
				$field,
				$executionField,
				$fieldType->getWrappedType(),
				$value,
			);

			if ($completedValue instanceof GuzzleHttp\Promise\PromiseInterface) {
				return $completedValue->then(
					static fn ($value) => new NonNullFieldValue($value),
				);
			} else {
				return new NonNullFieldValue($completedValue);
			}
		}

		if ($value === null) {
			return new ScalarFieldValue(null);
		}

		if ($fieldType instanceof GraphQL\Types\ListType) {
			if (is_iterable($value) === false) {
				return new ErrorFieldValue(
					new GraphQL\Error("List resolved to non-iterable value"),
				);
			}

			$itemType = $fieldType->getWrappedType();

			$result = [];

			foreach ($value as $item) {
				$result[] = $this->completeValue(
					$field,
					$executionField,
					$itemType,
					$item,
				);
			}

			return GuzzleHttp\Promise\Utils::all($result)->then(
				fn ($result) => new ListFieldValue(
					$itemType instanceof GraphQL\Types\NonNullType,
					$result,
				),
			);
		}

		$typeDefinition = $this->schema->getTypeDefinition($fieldType->name);

		if ($typeDefinition instanceof GraphQL\TypeSystem\EnumTypeDefinition) {
			if ($value instanceof BackedEnum) {
				$value = $value->value;
			}

			return new ScalarFieldValue($value);
		}

		if ($typeDefinition instanceof GraphQL\TypeSystem\TypeWithFieldSelection) {
			if (!$typeDefinition instanceof GraphQL\TypeSystem\ObjectTypeDefinition) {
				$abstractTypeResolver = $this->abstractTypeResolverProvider->getAbstractTypeResolver($typeDefinition->name);

				try {
					$objectTypeName = $abstractTypeResolver->resolveAbstractType($value);
				} catch (Throwable $e) {
					$this->errorHandler->handleAbstractTypeResolverError($e, $executionField, $value);

					return new ErrorFieldValue(
						new GraphQL\Error(
							sprintf(
								"Abstract type %s failed to resolve",
								$typeDefinition->name,
							),
						),
					);
				}

				$objectTypeDefinition = $this->schema->getTypeDefinitionOrNull($objectTypeName);

				if (
					$objectTypeDefinition === null
					|| !$objectTypeDefinition instanceof GraphQL\TypeSystem\ObjectTypeDefinition
				) {
					return new ErrorFieldValue(
						new GraphQL\Error(
							sprintf(
								"Abstract type %s was incorrectly resolved to %s type %s",
								$typeDefinition->name,
								$objectTypeDefinition !== null
									? $objectTypeDefinition->kind->format()
									: 'unknown',
								$objectTypeName,
							),
						),
					);
				}

				$typeDefinition = $objectTypeDefinition;
			}

			return $this->executeSelectionSet(
				$typeDefinition,
				$value,
				$executionField->selectionSet,
			);
		}

		try {
			return new ScalarFieldValue(
				$this->schema
					->scalarImplementationRegistry
					->getItem($typeDefinition->name)
					->serialize($value),
			);
		} catch (GraphQL\Exceptions\CannotSerializeScalarValueException $e) {
			return new ErrorFieldValue(
				new GraphQL\Error(
					$e->getMessage(),
					null,
					$e->extensions,
				),
			);
		} catch (Throwable $e) {
			$this->errorHandler->handleSerializeScalarError($e, $executionField, $value);

			return new ErrorFieldValue(
				new GraphQL\Error(
					sprintf(
						"Scalar type %s failed to serialize",
						$typeDefinition->name,
					),
				),
			);
		}
	}

}
