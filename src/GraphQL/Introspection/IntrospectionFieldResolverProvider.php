<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\Introspection;

use ReflectionClass;
use Vojtechdobes\GraphQL;


final class IntrospectionFieldResolverProvider implements GraphQL\FieldResolverProvider
{

	private readonly GraphQL\StaticFieldResolverProvider $fieldResolverProvider;



	public function __construct(GraphQL\TypeSystem\Schema $schema)
	{
		$this->fieldResolverProvider = new ReflectionClass(GraphQL\StaticFieldResolverProvider::class)->newLazyGhost(
			static fn ($object) => $object->__construct([
				'__Directive.args' => new DirectiveArgsFieldResolver(),
				'__Directive.description' => new GraphQL\PropertyFieldResolver(),
				'__Directive.isRepeatable' => new GraphQL\PropertyFieldResolver(),
				'__Directive.locations' => new GraphQL\PropertyFieldResolver(),
				'__Directive.name' => new GraphQL\PropertyFieldResolver(),
				'__EnumValue.deprecationReason' => new DeprecationReasonFieldResolver(),
				'__EnumValue.description' => new GraphQL\PropertyFieldResolver(),
				'__EnumValue.isDeprecated' => new IsDeprecatedFieldResolver(),
				'__EnumValue.name' => new GraphQL\PropertyFieldResolver(),
				'__Field.args' => new FieldArgsFieldResolver(),
				'__Field.deprecationReason' => new DeprecationReasonFieldResolver(),
				'__Field.description' => new GraphQL\PropertyFieldResolver(),
				'__Field.isDeprecated' => new IsDeprecatedFieldResolver(),
				'__Field.name' => new GraphQL\PropertyFieldResolver(),
				'__Field.type' => new GraphQL\PropertyFieldResolver(),
				'__InputValue.defaultValue' => new InputValueDefaultValueFieldResolver(),
				'__InputValue.description' => new GraphQL\PropertyFieldResolver(),
				'__InputValue.name' => new GraphQL\PropertyFieldResolver(),
				'__InputValue.type' => new GraphQL\PropertyFieldResolver(),
				'__Schema.description' => new SchemaDescriptionFieldResolver(),
				'__Schema.directives' => new SchemaDirectivesFieldResolver(),
				'__Schema.mutationType' => new SchemaRootOperationTypeFieldResolver(GraphQL\OperationType::Mutation),
				'__Schema.queryType' => new SchemaRootOperationTypeFieldResolver(GraphQL\OperationType::Query),
				'__Schema.subscriptionType' => new SchemaRootOperationTypeFieldResolver(GraphQL\OperationType::Subscription),
				'__Schema.types' => new SchemaTypesFieldResolver(),
				'__Type.description' => new TypeDescriptionFieldResolver($schema),
				'__Type.enumValues' => new TypeEnumValuesFieldResolver($schema),
				'__Type.fields' => new TypeFieldsFieldResolver($schema),
				'__Type.inputFields' => new TypeInputFieldsFieldResolver($schema),
				'__Type.interfaces' => new TypeInterfacesFieldResolver($schema),
				'__Type.kind' => new TypeKindFieldResolver($schema),
				'__Type.name' => new TypeNameFieldResolver(),
				'__Type.ofType' => new TypeOfTypeFieldResolver(),
				'__Type.possibleTypes' => new TypePossibleTypesFieldResolver($schema),
				'__Type.specifiedByURL' => new TypeSpecifiedByUrlFieldResolver($schema),
			]),
		);
	}



	public function hasFieldResolver(string $fieldName): bool
	{
		return str_starts_with($fieldName, '__');
	}



	public function getFieldResolver(string $fieldName): ?GraphQL\FieldResolver
	{
		if ($this->hasFieldResolver($fieldName) === false) {
			return null;
		}

		return $this->fieldResolverProvider->getFieldResolver($fieldName);
	}



	public function listSupportedFieldNames(): array
	{
		return $this->fieldResolverProvider->listSupportedFieldNames();
	}

}
