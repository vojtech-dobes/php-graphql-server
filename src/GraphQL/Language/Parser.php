<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\Language;

use Vojtechdobes\GrammarProcessing;
use Vojtechdobes\GraphQL;


/**
 * @phpstan-import-type PHPStan_ExecutableDefinition from GraphQL\Spec
 * @phpstan-import-type PHPStan_TypeSystemDefinition from GraphQL\Spec
 * @phpstan-import-type PHPStan_TypeSystemExtensionDocument from GraphQL\Spec
 */
final class Parser
{

	private readonly GrammarProcessing\Grammar $grammar;
	private readonly GrammarProcessing\Interpretation $interpretation;



	public function __construct()
	{
		$this->grammar = new GrammarFactory()->createGrammar();

		$this->interpretation = new GrammarProcessing\Interpretation([
			'Alias' => new Nodes\Alias(),
			'Argument' => new Nodes\Argument(),
			'Arguments' => new Nodes\Arguments(),
			'ArgumentsDefinition' => new Nodes\ArgumentsDefinition(),
			'BooleanValue' => new Nodes\BooleanValue(),
			'DefaultValue' => new Nodes\DefaultValue(),
			'Description' => new Nodes\Description(),
			'Directive' => new Nodes\Directive(),
			'DirectiveDefinition' => new Nodes\DirectiveDefinition(),
			'DirectiveLocation' => new Nodes\DirectiveLocation(),
			'DirectiveLocations' => new Nodes\DirectiveLocations(),
			'Directives' => new Nodes\Directives(),
			'EnumTypeDefinition' => new Nodes\EnumTypeDefinition(),
			'EnumTypeExtension' => new Nodes\EnumTypeExtension(),
			'EnumValue' => new Nodes\EnumValue(),
			'EnumValueDefinition' => new Nodes\EnumValueDefinition(),
			'EnumValuesDefinition' => new Nodes\EnumValuesDefinition(),
			'ExecutableDefinition' => new Nodes\ExecutableDefinition(),
			'ExecutableDirectiveLocation' => new Nodes\ExecutableDirectiveLocation(),
			'ExecutableDocument' => new Nodes\ExecutableDocument(),
			'Field' => new Nodes\Field(),
			'FieldDefinition' => new Nodes\FieldDefinition(),
			'FieldsDefinition' => new Nodes\FieldsDefinition(),
			'FloatValue' => new Nodes\FloatValue(),
			'FragmentDefinition' => new Nodes\FragmentDefinition(),
			'FragmentName' => new Nodes\FragmentName(),
			'FragmentSpread' => new Nodes\FragmentSpread(),
			'ImplementsInterfaces' => new Nodes\ImplementsInterfaces(),
			'InlineFragment' => new Nodes\InlineFragment(),
			'InputFieldsDefinition' => new Nodes\InputFieldsDefinition(),
			'InputObjectTypeDefinition' => new Nodes\InputObjectTypeDefinition(),
			'InputObjectTypeExtension' => new Nodes\InputObjectTypeExtension(),
			'InputValueDefinition' => new Nodes\InputValueDefinition(),
			'IntValue' => new Nodes\IntValue(),
			'InterfaceTypeDefinition' => new Nodes\InterfaceTypeDefinition(),
			'InterfaceTypeExtension' => new Nodes\InterfaceTypeExtension(),
			'ListType' => new Nodes\ListType(),
			'ListValue' => new Nodes\ListValue(),
			'NamedType' => new Nodes\NamedType(),
			'NonNullType' => new Nodes\NonNullType(),
			'NullValue' => new Nodes\NullValue(),
			'ObjectField' => new Nodes\ObjectField(),
			'ObjectTypeDefinition' => new Nodes\ObjectTypeDefinition(),
			'ObjectTypeExtension' => new Nodes\ObjectTypeExtension(),
			'ObjectValue' => new Nodes\ObjectValue(),
			'OperationDefinition' => new Nodes\OperationDefinition(),
			'OperationType' => new Nodes\OperationType(),
			'RootOperationTypeDefinition' => new Nodes\RootOperationTypeDefinition(),
			'ScalarTypeDefinition' => new Nodes\ScalarTypeDefinition(),
			'ScalarTypeExtension' => new Nodes\ScalarTypeExtension(),
			'SchemaDefinition' => new Nodes\SchemaDefinition(),
			'SchemaExtension' => new Nodes\SchemaExtension(),
			'Selection' => new Nodes\Selection(),
			'SelectionSet' => new Nodes\SelectionSet(),
			'StringValue' => new Nodes\StringValue(),
			'Type' => new Nodes\Type(),
			'TypeCondition' => new Nodes\TypeCondition(),
			'TypeDefinition' => new Nodes\TypeDefinition(),
			'TypeExtension' => new Nodes\TypeExtension(),
			'TypeSystemDefinition' => new Nodes\TypeSystemDefinition(),
			'TypeSystemDefinitionOrExtension' => new Nodes\TypeSystemDefinitionOrExtension(),
			'TypeSystemDirectiveLocation' => new Nodes\TypeSystemDirectiveLocation(),
			'TypeSystemDocument' => new Nodes\TypeSystemDocument(),
			'TypeSystemExtension' => new Nodes\TypeSystemExtension(),
			'TypeSystemExtensionDocument' => new Nodes\TypeSystemExtensionDocument(),
			'UnionMemberTypes' => new Nodes\UnionMemberTypes(),
			'UnionTypeDefinition' => new Nodes\UnionTypeDefinition(),
			'UnionTypeExtension' => new Nodes\UnionTypeExtension(),
			'Value' => new Nodes\Value(),
			'Variable' => new Nodes\Variable(),
			'VariableDefinition' => new Nodes\VariableDefinition(),
			'VariableDefinitions' => new Nodes\VariableDefinitions(),
		]);
	}



	/**
	 * @return list<PHPStan_ExecutableDefinition>
	 * @throws GraphQL\Exceptions\CannotParseDocumentException
	 */
	public function parseExecutableDocument(string $document): array
	{
		return $this->parse($document, 'ExecutableDocument');
	}



	/**
	 * @return list<PHPStan_TypeSystemDefinition>
	 * @throws GraphQL\Exceptions\CannotParseDocumentException
	 */
	public function parseTypeSystemDocument(string $document): array
	{
		return $this->parse($document, 'TypeSystemDocument');
	}



	/**
	 * @return list<PHPStan_TypeSystemExtensionDocument>
	 * @throws GraphQL\Exceptions\CannotParseDocumentException
	 */
	public function parseTypeSystemExtensionDocument(string $document): array
	{
		return $this->parse($document, 'TypeSystemExtensionDocument');
	}



	/**
	 * @throws GraphQL\Exceptions\CannotParseDocumentException
	 */
	public function parseType(string $document): GraphQL\Types\Type
	{
		return $this->parse($document, 'Type');
	}



	/**
	 * @throws GraphQL\Exceptions\CannotParseDocumentException
	 */
	public function parseDescription(string $document): string
	{
		return $this->parse($document, 'Description');
	}



	/**
	 * @throws GraphQL\Exceptions\CannotParseDocumentException
	 */
	public function parse(string $document, string $rootSymbol): mixed
	{
		try {
			return $this->grammar
				->parseSource($document, $rootSymbol)
				->interpret($this->interpretation);
		} catch (GrammarProcessing\CannotConsumeTokenException $e) {
			$error = new GraphQL\Error($e->getMessage());

			if ($e->location !== null) {
				$error = $error->withLocation(
					line: $e->location->line,
					column: $e->location->column,
				);
			}

			throw new GraphQL\Exceptions\CannotParseDocumentException([$error], $e);
		}
	}

}
