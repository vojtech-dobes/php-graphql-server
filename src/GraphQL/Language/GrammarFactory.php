<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\Language;

use Vojtechdobes\GrammarProcessing;


final class GrammarFactory
{

	public function createGrammar(): GrammarProcessing\Grammar
	{
		$ignoredTokenSymbols = [
			'Ignored',
		];

		$syntaxTokenSymbols = [
			'Punctuator',
			'Name',
			'IntValue',
			'FloatValue',
			'StringValue',
		];

		$lexicalSymbols = [
			'SourceCharacter' => new GrammarProcessing\Vocabulary\OneOf([
				new GrammarProcessing\Vocabulary\Regexp('\x{0009}'),
				new GrammarProcessing\Vocabulary\Regexp('\x{000A}'),
				new GrammarProcessing\Vocabulary\Regexp('\x{000D}'),
				new GrammarProcessing\Vocabulary\Regexp('[\x{0020}-\x{FFFF}]'),
			]),
			'Ignored' => new GrammarProcessing\Vocabulary\OneOf([
				new GrammarProcessing\Vocabulary\Nonterminal('UnicodeBOM'),
				new GrammarProcessing\Vocabulary\Nonterminal('WhiteSpace'),
				new GrammarProcessing\Vocabulary\Nonterminal('LineTerminator'),
				new GrammarProcessing\Vocabulary\Nonterminal('Comment'),
				new GrammarProcessing\Vocabulary\Nonterminal('Comma'),
			]),
			'UnicodeBOM' => new GrammarProcessing\Vocabulary\Regexp('\x{FEFF}'),
			'WhiteSpace' => new GrammarProcessing\Vocabulary\OneOf([
				new GrammarProcessing\Vocabulary\Regexp('\x{0009}'),
				new GrammarProcessing\Vocabulary\Regexp('\x{0020}'),
			]),
			'LineTerminator' => new GrammarProcessing\Vocabulary\OneOf([
				new GrammarProcessing\Vocabulary\Regexp('\x{000A}'),
				new GrammarProcessing\Vocabulary\Sequence([
					new GrammarProcessing\Vocabulary\Regexp('\x{000D}'),
					new GrammarProcessing\Vocabulary\NegativeLookahead(new GrammarProcessing\Vocabulary\Regexp('\x{000A}')),
				]),
				new GrammarProcessing\Vocabulary\Sequence([
					new GrammarProcessing\Vocabulary\Regexp('\x{000D}'),
					new GrammarProcessing\Vocabulary\Regexp('\x{000A}'),
				]),
			]),
			'Comment' => new GrammarProcessing\Vocabulary\Sequence([
				new GrammarProcessing\Vocabulary\Literal('#'),
				new GrammarProcessing\Vocabulary\Repeat(new GrammarProcessing\Vocabulary\Nonterminal('CommentChar'), 0, null),
				new GrammarProcessing\Vocabulary\NegativeLookahead(new GrammarProcessing\Vocabulary\Nonterminal('CommentChar')),
			]),
			'CommentChar' => new GrammarProcessing\Vocabulary\Subtract(
				new GrammarProcessing\Vocabulary\Nonterminal('SourceCharacter'),
				new GrammarProcessing\Vocabulary\Nonterminal('LineTerminator'),
			),
			'Comma' => new GrammarProcessing\Vocabulary\Literal(','),
			'Punctuator' => new GrammarProcessing\Vocabulary\OneOf([
				new GrammarProcessing\Vocabulary\Literal('!'),
				new GrammarProcessing\Vocabulary\Literal('$'),
				new GrammarProcessing\Vocabulary\Literal('&'),
				new GrammarProcessing\Vocabulary\Literal('('),
				new GrammarProcessing\Vocabulary\Literal(')'),
				new GrammarProcessing\Vocabulary\Literal('...'),
				new GrammarProcessing\Vocabulary\Literal(':'),
				new GrammarProcessing\Vocabulary\Literal('='),
				new GrammarProcessing\Vocabulary\Literal('@'),
				new GrammarProcessing\Vocabulary\Literal('['),
				new GrammarProcessing\Vocabulary\Literal(']'),
				new GrammarProcessing\Vocabulary\Literal('{'),
				new GrammarProcessing\Vocabulary\Literal('|'),
				new GrammarProcessing\Vocabulary\Literal('}'),
			]),
			'Name' => new GrammarProcessing\Vocabulary\Sequence([
				new GrammarProcessing\Vocabulary\Nonterminal('NameStart'),
				new GrammarProcessing\Vocabulary\Repeat(new GrammarProcessing\Vocabulary\Nonterminal('NameContinue'), min: 0, max: null),
				new GrammarProcessing\Vocabulary\NegativeLookahead(new GrammarProcessing\Vocabulary\Nonterminal('NameContinue')),
			]),
			'NameStart' => new GrammarProcessing\Vocabulary\OneOf([
				new GrammarProcessing\Vocabulary\Nonterminal('Letter'),
				new GrammarProcessing\Vocabulary\Literal('_'),
			]),
			'NameContinue' => new GrammarProcessing\Vocabulary\OneOf([
				new GrammarProcessing\Vocabulary\Nonterminal('Letter'),
				new GrammarProcessing\Vocabulary\Nonterminal('Digit'),
				new GrammarProcessing\Vocabulary\Literal('_'),
			]),
			'Letter' => new GrammarProcessing\Vocabulary\Regexp('[a-zA-Z]'),
			'Digit' => new GrammarProcessing\Vocabulary\Regexp('[0-9]'),
			'IntValue' => new GrammarProcessing\Vocabulary\Sequence([
				new GrammarProcessing\Vocabulary\Nonterminal('IntegerPart'),
				new GrammarProcessing\Vocabulary\NegativeLookahead(new GrammarProcessing\Vocabulary\OneOf([
					new GrammarProcessing\Vocabulary\Nonterminal('Digit'),
					new GrammarProcessing\Vocabulary\Literal('.'),
					new GrammarProcessing\Vocabulary\Nonterminal('NameStart'),
				])),
			]),
			'IntegerPart' => new GrammarProcessing\Vocabulary\OneOf([
				new GrammarProcessing\Vocabulary\Sequence([
					new GrammarProcessing\Vocabulary\Repeat(new GrammarProcessing\Vocabulary\Nonterminal('NegativeSign'), 0, 1),
					new GrammarProcessing\Vocabulary\Literal('0'),
				]),
				new GrammarProcessing\Vocabulary\Sequence([
					new GrammarProcessing\Vocabulary\Repeat(new GrammarProcessing\Vocabulary\Nonterminal('NegativeSign'), 0, 1),
					new GrammarProcessing\Vocabulary\Nonterminal('NonZeroDigit'),
					new GrammarProcessing\Vocabulary\Repeat(new GrammarProcessing\Vocabulary\Nonterminal('Digit'), 0, null),
				]),
			]),
			'NegativeSign' => new GrammarProcessing\Vocabulary\Literal('-'),
			'NonZeroDigit' => new GrammarProcessing\Vocabulary\Subtract(
				new GrammarProcessing\Vocabulary\Nonterminal('Digit'),
				new GrammarProcessing\Vocabulary\Literal('0'),
			),
			'FloatValue' => new GrammarProcessing\Vocabulary\OneOf([
				new GrammarProcessing\Vocabulary\Sequence([
					new GrammarProcessing\Vocabulary\Nonterminal('IntegerPart'),
					new GrammarProcessing\Vocabulary\Nonterminal('FractionalPart'),
					new GrammarProcessing\Vocabulary\Nonterminal('ExponentPart'),
				]),
				new GrammarProcessing\Vocabulary\Sequence([
					new GrammarProcessing\Vocabulary\Nonterminal('IntegerPart'),
					new GrammarProcessing\Vocabulary\Nonterminal('FractionalPart'),
				]),
				new GrammarProcessing\Vocabulary\Sequence([
					new GrammarProcessing\Vocabulary\Nonterminal('IntegerPart'),
					new GrammarProcessing\Vocabulary\Nonterminal('ExponentPart'),
				]),
			]),
			'FractionalPart' => new GrammarProcessing\Vocabulary\Sequence([
				new GrammarProcessing\Vocabulary\Literal('.'),
				new GrammarProcessing\Vocabulary\Repeat(new GrammarProcessing\Vocabulary\Nonterminal('Digit'), 1, null),
			]),
			'ExponentPart' => new GrammarProcessing\Vocabulary\Sequence([
				new GrammarProcessing\Vocabulary\Nonterminal('ExponentIndicator'),
				new GrammarProcessing\Vocabulary\Repeat(new GrammarProcessing\Vocabulary\Nonterminal('Sign'), 0, 1),
				new GrammarProcessing\Vocabulary\Repeat(new GrammarProcessing\Vocabulary\Nonterminal('Digit'), 1, null),
			]),
			'ExponentIndicator' => new GrammarProcessing\Vocabulary\OneOf([
				new GrammarProcessing\Vocabulary\Literal('e'),
				new GrammarProcessing\Vocabulary\Literal('E'),
			]),
			'Sign' => new GrammarProcessing\Vocabulary\OneOf([
				new GrammarProcessing\Vocabulary\Literal('+'),
				new GrammarProcessing\Vocabulary\Literal('-'),
			]),
			'StringValue' => new GrammarProcessing\Vocabulary\OneOf([
				new GrammarProcessing\Vocabulary\Sequence([
					new GrammarProcessing\Vocabulary\Literal('""'),
					new GrammarProcessing\Vocabulary\NegativeLookahead(new GrammarProcessing\Vocabulary\Literal('"')),
				]),
				new GrammarProcessing\Vocabulary\Sequence([
					new GrammarProcessing\Vocabulary\Literal('"'),
					new GrammarProcessing\Vocabulary\Repeat(new GrammarProcessing\Vocabulary\Nonterminal('StringCharacter'), 1, null),
					new GrammarProcessing\Vocabulary\Literal('"'),
				]),
				new GrammarProcessing\Vocabulary\Sequence([
					new GrammarProcessing\Vocabulary\Literal('"""'),
					new GrammarProcessing\Vocabulary\Repeat(new GrammarProcessing\Vocabulary\Nonterminal('BlockStringCharacter'), 0, null),
					new GrammarProcessing\Vocabulary\Literal('"""'),
				]),
			]),
			'StringCharacter' => new GrammarProcessing\Vocabulary\OneOf([
				new GrammarProcessing\Vocabulary\Subtract(
					new GrammarProcessing\Vocabulary\Nonterminal('SourceCharacter'),
					new GrammarProcessing\Vocabulary\OneOf([
						new GrammarProcessing\Vocabulary\Literal('"'),
						new GrammarProcessing\Vocabulary\Literal('\\'),
						new GrammarProcessing\Vocabulary\Nonterminal('LineTerminator'),
					]),
				),
				new GrammarProcessing\Vocabulary\Sequence([
					new GrammarProcessing\Vocabulary\Literal('\u'),
					new GrammarProcessing\Vocabulary\Nonterminal('EscapedUnicode'),
				]),
				new GrammarProcessing\Vocabulary\Sequence([
					new GrammarProcessing\Vocabulary\Literal('\\'),
					new GrammarProcessing\Vocabulary\Nonterminal('EscapedCharacter'),
				]),
			]),
			'EscapedUnicode' => new GrammarProcessing\Vocabulary\Regexp('[0-9A-Fa-f]{4}'),
			'EscapedCharacter' => new GrammarProcessing\Vocabulary\OneOf([
				new GrammarProcessing\Vocabulary\Literal('"'),
				new GrammarProcessing\Vocabulary\Literal('\\'),
				new GrammarProcessing\Vocabulary\Literal('/'),
				new GrammarProcessing\Vocabulary\Literal('b'),
				new GrammarProcessing\Vocabulary\Literal('f'),
				new GrammarProcessing\Vocabulary\Literal('n'),
				new GrammarProcessing\Vocabulary\Literal('r'),
				new GrammarProcessing\Vocabulary\Literal('t'),
			]),
			'BlockStringCharacter' => new GrammarProcessing\Vocabulary\OneOf([
				new GrammarProcessing\Vocabulary\Subtract(
					new GrammarProcessing\Vocabulary\Nonterminal('SourceCharacter'),
					new GrammarProcessing\Vocabulary\OneOf([
						new GrammarProcessing\Vocabulary\Literal('"""'),
						new GrammarProcessing\Vocabulary\Literal('\"""'),
					]),
				),
				new GrammarProcessing\Vocabulary\Literal('\"""'),
			]),
		];

		$syntacticSymbols = [
			'Document' => new GrammarProcessing\Vocabulary\Repeat(new GrammarProcessing\Vocabulary\Nonterminal('Definition'), min: 1, max: null),
			'Definition' => new GrammarProcessing\Vocabulary\OneOf([
				new GrammarProcessing\Vocabulary\Nonterminal('ExecutableDefinition'),
				new GrammarProcessing\Vocabulary\Nonterminal('TypeSystemDefinitionOrExtension'),
			]),
			'ExecutableDocument' => new GrammarProcessing\Vocabulary\Repeat(new GrammarProcessing\Vocabulary\Nonterminal('ExecutableDefinition'), min: 1, max: null),
			'ExecutableDefinition' => new GrammarProcessing\Vocabulary\OneOf([
				new GrammarProcessing\Vocabulary\Nonterminal('OperationDefinition'),
				new GrammarProcessing\Vocabulary\Nonterminal('FragmentDefinition'),
			]),
			'OperationDefinition' => new GrammarProcessing\Vocabulary\OneOf([
				new GrammarProcessing\Vocabulary\Sequence([
					new GrammarProcessing\Vocabulary\Nonterminal('OperationType'),
					new GrammarProcessing\Vocabulary\Repeat(new GrammarProcessing\Vocabulary\Nonterminal('Name'), min: 0, max: 1),
					new GrammarProcessing\Vocabulary\Repeat(new GrammarProcessing\Vocabulary\Nonterminal('VariableDefinitions'), min: 0, max: 1),
					new GrammarProcessing\Vocabulary\Repeat(new GrammarProcessing\Vocabulary\Nonterminal('Directives'), min: 0, max: 1),
					new GrammarProcessing\Vocabulary\Nonterminal('SelectionSet'),
				]),
				new GrammarProcessing\Vocabulary\Nonterminal('SelectionSet'),
			]),
			'OperationType' => new GrammarProcessing\Vocabulary\OneOf([
				new GrammarProcessing\Vocabulary\Literal('query'),
				new GrammarProcessing\Vocabulary\Literal('mutation'),
				new GrammarProcessing\Vocabulary\Literal('subscription'),
			]),
			'SelectionSet' => new GrammarProcessing\Vocabulary\Sequence([
				new GrammarProcessing\Vocabulary\Literal('{'),
				new GrammarProcessing\Vocabulary\Repeat(new GrammarProcessing\Vocabulary\Nonterminal('Selection'), 1, null),
				new GrammarProcessing\Vocabulary\Literal('}'),
			]),
			'Selection' => new GrammarProcessing\Vocabulary\OneOf([
				new GrammarProcessing\Vocabulary\Nonterminal('Field'),
				new GrammarProcessing\Vocabulary\Nonterminal('FragmentSpread'),
				new GrammarProcessing\Vocabulary\Nonterminal('InlineFragment'),
			]),
			'Field' => new GrammarProcessing\Vocabulary\Sequence([
				new GrammarProcessing\Vocabulary\Repeat(new GrammarProcessing\Vocabulary\Nonterminal('Alias'), 0, 1),
				new GrammarProcessing\Vocabulary\Nonterminal('Name'),
				new GrammarProcessing\Vocabulary\Repeat(new GrammarProcessing\Vocabulary\Nonterminal('Arguments'), 0, 1),
				new GrammarProcessing\Vocabulary\Repeat(new GrammarProcessing\Vocabulary\Nonterminal('Directives'), 0, 1),
				new GrammarProcessing\Vocabulary\Repeat(new GrammarProcessing\Vocabulary\Nonterminal('SelectionSet'), 0, 1),
			]),
			'Alias' => new GrammarProcessing\Vocabulary\Sequence([
				new GrammarProcessing\Vocabulary\Nonterminal('Name'),
				new GrammarProcessing\Vocabulary\Literal(':'),
			]),
			'Arguments' => new GrammarProcessing\Vocabulary\Sequence([
				new GrammarProcessing\Vocabulary\Literal('('),
				new GrammarProcessing\Vocabulary\Repeat(new GrammarProcessing\Vocabulary\Nonterminal('Argument'), 1, null),
				new GrammarProcessing\Vocabulary\Literal(')'),
			]),
			'Argument' => new GrammarProcessing\Vocabulary\Sequence([
				new GrammarProcessing\Vocabulary\Nonterminal('Name'),
				new GrammarProcessing\Vocabulary\Literal(':'),
				new GrammarProcessing\Vocabulary\Nonterminal('Value'),
			]),
			'FragmentSpread' => new GrammarProcessing\Vocabulary\Sequence([
				new GrammarProcessing\Vocabulary\Literal('...'),
				new GrammarProcessing\Vocabulary\Nonterminal('FragmentName'),
				new GrammarProcessing\Vocabulary\Repeat(new GrammarProcessing\Vocabulary\Nonterminal('Directives'), 0, 1),
			]),
			'InlineFragment' => new GrammarProcessing\Vocabulary\Sequence([
				new GrammarProcessing\Vocabulary\Literal('...'),
				new GrammarProcessing\Vocabulary\Repeat(new GrammarProcessing\Vocabulary\Nonterminal('TypeCondition'), 0, 1),
				new GrammarProcessing\Vocabulary\Repeat(new GrammarProcessing\Vocabulary\Nonterminal('Directives'), 0, 1),
				new GrammarProcessing\Vocabulary\Nonterminal('SelectionSet'),
			]),
			'FragmentDefinition' => new GrammarProcessing\Vocabulary\Sequence([
				new GrammarProcessing\Vocabulary\Literal('fragment'),
				new GrammarProcessing\Vocabulary\Nonterminal('FragmentName'),
				new GrammarProcessing\Vocabulary\Nonterminal('TypeCondition'),
				new GrammarProcessing\Vocabulary\Repeat(new GrammarProcessing\Vocabulary\Nonterminal('Directives'), 0, 1),
				new GrammarProcessing\Vocabulary\Nonterminal('SelectionSet'),
			]),
			'FragmentName' => new GrammarProcessing\Vocabulary\Subtract(
				new GrammarProcessing\Vocabulary\Nonterminal('Name'),
				new GrammarProcessing\Vocabulary\Literal('on'),
			),
			'TypeCondition' => new GrammarProcessing\Vocabulary\Sequence([
				new GrammarProcessing\Vocabulary\Literal('on'),
				new GrammarProcessing\Vocabulary\Nonterminal('NamedType'),
			]),
			'Value' => new GrammarProcessing\Vocabulary\OneOf([
				new GrammarProcessing\Vocabulary\Nonterminal('Variable'),
				new GrammarProcessing\Vocabulary\Nonterminal('IntValue'),
				new GrammarProcessing\Vocabulary\Nonterminal('FloatValue'),
				new GrammarProcessing\Vocabulary\Nonterminal('StringValue'),
				new GrammarProcessing\Vocabulary\Nonterminal('BooleanValue'),
				new GrammarProcessing\Vocabulary\Nonterminal('NullValue'),
				new GrammarProcessing\Vocabulary\Nonterminal('EnumValue'),
				new GrammarProcessing\Vocabulary\Nonterminal('ListValue'),
				new GrammarProcessing\Vocabulary\Nonterminal('ObjectValue'),
			]),
			'BooleanValue' => new GrammarProcessing\Vocabulary\OneOf([
				new GrammarProcessing\Vocabulary\Literal('true'),
				new GrammarProcessing\Vocabulary\Literal('false'),
			]),
			'NullValue' => new GrammarProcessing\Vocabulary\Literal('null'),
			'EnumValue' => new GrammarProcessing\Vocabulary\Subtract(
				new GrammarProcessing\Vocabulary\Nonterminal('Name'),
				new GrammarProcessing\Vocabulary\OneOf([
					new GrammarProcessing\Vocabulary\Literal('true'),
					new GrammarProcessing\Vocabulary\Literal('false'),
					new GrammarProcessing\Vocabulary\Literal('null'),
				]),
			),
			'ListValue' => new GrammarProcessing\Vocabulary\OneOf([
				new GrammarProcessing\Vocabulary\Sequence([
					new GrammarProcessing\Vocabulary\Literal('['),
					new GrammarProcessing\Vocabulary\Literal(']'),
				]),
				new GrammarProcessing\Vocabulary\Sequence([
					new GrammarProcessing\Vocabulary\Literal('['),
					new GrammarProcessing\Vocabulary\Repeat(new GrammarProcessing\Vocabulary\Nonterminal('Value'), 1, null),
					new GrammarProcessing\Vocabulary\Literal(']'),
				]),
			]),
			'ObjectValue' => new GrammarProcessing\Vocabulary\OneOf([
				new GrammarProcessing\Vocabulary\Sequence([
					new GrammarProcessing\Vocabulary\Literal('{'),
					new GrammarProcessing\Vocabulary\Literal('}'),
				]),
				new GrammarProcessing\Vocabulary\Sequence([
					new GrammarProcessing\Vocabulary\Literal('{'),
					new GrammarProcessing\Vocabulary\Repeat(new GrammarProcessing\Vocabulary\Nonterminal('ObjectField'), 1, null),
					new GrammarProcessing\Vocabulary\Literal('}'),
				]),
			]),
			'ObjectField' => new GrammarProcessing\Vocabulary\Sequence([
				new GrammarProcessing\Vocabulary\Nonterminal('Name'),
				new GrammarProcessing\Vocabulary\Literal(':'),
				new GrammarProcessing\Vocabulary\Nonterminal('Value'),
			]),
			'VariableDefinitions' => new GrammarProcessing\Vocabulary\Sequence([
				new GrammarProcessing\Vocabulary\Literal('('),
				new GrammarProcessing\Vocabulary\Repeat(new GrammarProcessing\Vocabulary\Nonterminal('VariableDefinition'), 1, null),
				new GrammarProcessing\Vocabulary\Literal(')'),
			]),
			'VariableDefinition' => new GrammarProcessing\Vocabulary\Sequence([
				new GrammarProcessing\Vocabulary\Nonterminal('Variable'),
				new GrammarProcessing\Vocabulary\Literal(':'),
				new GrammarProcessing\Vocabulary\Nonterminal('Type'),
				new GrammarProcessing\Vocabulary\Repeat(new GrammarProcessing\Vocabulary\Nonterminal('DefaultValue'), 0, 1),
				new GrammarProcessing\Vocabulary\Repeat(new GrammarProcessing\Vocabulary\Nonterminal('Directives'), 0, 1),
			]),
			'Variable' => new GrammarProcessing\Vocabulary\Sequence([
				new GrammarProcessing\Vocabulary\Literal('$'),
				new GrammarProcessing\Vocabulary\Nonterminal('Name'),
			]),
			'DefaultValue' => new GrammarProcessing\Vocabulary\Sequence([
				new GrammarProcessing\Vocabulary\Literal('='),
				new GrammarProcessing\Vocabulary\Nonterminal('Value'),
			]),
			'Type' => new GrammarProcessing\Vocabulary\OneOf([
				new GrammarProcessing\Vocabulary\Nonterminal('NamedType'),
				new GrammarProcessing\Vocabulary\Nonterminal('ListType'),
				new GrammarProcessing\Vocabulary\Nonterminal('NonNullType'),
			]),
			'NamedType' => new GrammarProcessing\Vocabulary\Nonterminal('Name'),
			'ListType' => new GrammarProcessing\Vocabulary\Sequence([
				new GrammarProcessing\Vocabulary\Literal('['),
				new GrammarProcessing\Vocabulary\Nonterminal('Type'),
				new GrammarProcessing\Vocabulary\Literal(']'),
			]),
			'NonNullType' => new GrammarProcessing\Vocabulary\OneOf([
				new GrammarProcessing\Vocabulary\Sequence([
					new GrammarProcessing\Vocabulary\Nonterminal('NamedType'),
					new GrammarProcessing\Vocabulary\Literal('!'),
				]),
				new GrammarProcessing\Vocabulary\Sequence([
					new GrammarProcessing\Vocabulary\Nonterminal('ListType'),
					new GrammarProcessing\Vocabulary\Literal('!'),
				]),
			]),
			'Directives' => new GrammarProcessing\Vocabulary\Repeat(new GrammarProcessing\Vocabulary\Nonterminal('Directive'), 1, null),
			'Directive' => new GrammarProcessing\Vocabulary\Sequence([
				new GrammarProcessing\Vocabulary\Literal('@'),
				new GrammarProcessing\Vocabulary\Nonterminal('Name'),
				new GrammarProcessing\Vocabulary\Repeat(new GrammarProcessing\Vocabulary\Nonterminal('Arguments'), 0, 1),
			]),
			'TypeSystemDocument' => new GrammarProcessing\Vocabulary\Repeat(new GrammarProcessing\Vocabulary\Nonterminal('TypeSystemDefinition'), 1, null),
			'TypeSystemDefinition' => new GrammarProcessing\Vocabulary\OneOf([
				new GrammarProcessing\Vocabulary\Nonterminal('SchemaDefinition'),
				new GrammarProcessing\Vocabulary\Nonterminal('TypeDefinition'),
				new GrammarProcessing\Vocabulary\Nonterminal('DirectiveDefinition'),
			]),
			'TypeSystemExtensionDocument' => new GrammarProcessing\Vocabulary\Repeat(new GrammarProcessing\Vocabulary\Nonterminal('TypeSystemDefinitionOrExtension'), 1, null),
			'TypeSystemDefinitionOrExtension' => new GrammarProcessing\Vocabulary\OneOf([
				new GrammarProcessing\Vocabulary\Nonterminal('TypeSystemDefinition'),
				new GrammarProcessing\Vocabulary\Nonterminal('TypeSystemExtension'),
			]),
			'TypeSystemExtension' => new GrammarProcessing\Vocabulary\OneOf([
				new GrammarProcessing\Vocabulary\Nonterminal('SchemaExtension'),
				new GrammarProcessing\Vocabulary\Nonterminal('TypeExtension'),
			]),
			'SchemaDefinition' => new GrammarProcessing\Vocabulary\Sequence([
				new GrammarProcessing\Vocabulary\Repeat(new GrammarProcessing\Vocabulary\Nonterminal('Description'), 0, 1),
				new GrammarProcessing\Vocabulary\Literal('schema'),
				new GrammarProcessing\Vocabulary\Repeat(new GrammarProcessing\Vocabulary\Nonterminal('Directives'), 0, 1),
				new GrammarProcessing\Vocabulary\Literal('{'),
				new GrammarProcessing\Vocabulary\Repeat(new GrammarProcessing\Vocabulary\Nonterminal('RootOperationTypeDefinition'), 1, null),
				new GrammarProcessing\Vocabulary\Literal('}'),
			]),
			'SchemaExtension' => new GrammarProcessing\Vocabulary\OneOf([
				new GrammarProcessing\Vocabulary\Sequence([
					new GrammarProcessing\Vocabulary\Literal('extend'),
					new GrammarProcessing\Vocabulary\Literal('schema'),
					new GrammarProcessing\Vocabulary\Repeat(new GrammarProcessing\Vocabulary\Nonterminal('Directives'), 0, 1),
					new GrammarProcessing\Vocabulary\Literal('{'),
					new GrammarProcessing\Vocabulary\Repeat(new GrammarProcessing\Vocabulary\Nonterminal('RootOperationTypeDefinition'), 1, null),
					new GrammarProcessing\Vocabulary\Literal('}'),
				]),
				new GrammarProcessing\Vocabulary\Sequence([
					new GrammarProcessing\Vocabulary\Literal('extend'),
					new GrammarProcessing\Vocabulary\Literal('schema'),
					new GrammarProcessing\Vocabulary\Nonterminal('Directives'),
				]),
			]),
			'RootOperationTypeDefinition' => new GrammarProcessing\Vocabulary\Sequence([
				new GrammarProcessing\Vocabulary\Nonterminal('OperationType'),
				new GrammarProcessing\Vocabulary\Literal(':'),
				new GrammarProcessing\Vocabulary\Nonterminal('NamedType'),
			]),
			'Description' => new GrammarProcessing\Vocabulary\Nonterminal('StringValue'),
			'TypeDefinition' => new GrammarProcessing\Vocabulary\OneOf([
				new GrammarProcessing\Vocabulary\Nonterminal('ScalarTypeDefinition'),
				new GrammarProcessing\Vocabulary\Nonterminal('ObjectTypeDefinition'),
				new GrammarProcessing\Vocabulary\Nonterminal('InterfaceTypeDefinition'),
				new GrammarProcessing\Vocabulary\Nonterminal('UnionTypeDefinition'),
				new GrammarProcessing\Vocabulary\Nonterminal('EnumTypeDefinition'),
				new GrammarProcessing\Vocabulary\Nonterminal('InputObjectTypeDefinition'),
			]),
			'TypeExtension' => new GrammarProcessing\Vocabulary\OneOf([
				new GrammarProcessing\Vocabulary\Nonterminal('ScalarTypeExtension'),
				new GrammarProcessing\Vocabulary\Nonterminal('ObjectTypeExtension'),
				new GrammarProcessing\Vocabulary\Nonterminal('InterfaceTypeExtension'),
				new GrammarProcessing\Vocabulary\Nonterminal('UnionTypeExtension'),
				new GrammarProcessing\Vocabulary\Nonterminal('EnumTypeExtension'),
				new GrammarProcessing\Vocabulary\Nonterminal('InputObjectTypeExtension'),
			]),
			'ScalarTypeDefinition' => new GrammarProcessing\Vocabulary\Sequence([
				new GrammarProcessing\Vocabulary\Repeat(new GrammarProcessing\Vocabulary\Nonterminal('Description'), 0, 1),
				new GrammarProcessing\Vocabulary\Literal('scalar'),
				new GrammarProcessing\Vocabulary\Nonterminal('Name'),
				new GrammarProcessing\Vocabulary\Repeat(new GrammarProcessing\Vocabulary\Nonterminal('Directives'), 0, 1),
			]),
			'ScalarTypeExtension' => new GrammarProcessing\Vocabulary\Sequence([
				new GrammarProcessing\Vocabulary\Literal('extend'),
				new GrammarProcessing\Vocabulary\Literal('scalar'),
				new GrammarProcessing\Vocabulary\Nonterminal('Name'),
				new GrammarProcessing\Vocabulary\Nonterminal('Directives'),
			]),
			'ObjectTypeDefinition' => new GrammarProcessing\Vocabulary\Sequence([
				new GrammarProcessing\Vocabulary\Repeat(new GrammarProcessing\Vocabulary\Nonterminal('Description'), 0, 1),
				new GrammarProcessing\Vocabulary\Literal('type'),
				new GrammarProcessing\Vocabulary\Nonterminal('Name'),
				new GrammarProcessing\Vocabulary\Repeat(new GrammarProcessing\Vocabulary\Nonterminal('ImplementsInterfaces'), 0, 1),
				new GrammarProcessing\Vocabulary\Repeat(new GrammarProcessing\Vocabulary\Nonterminal('Directives'), 0, 1),
				new GrammarProcessing\Vocabulary\Repeat(new GrammarProcessing\Vocabulary\Nonterminal('FieldsDefinition'), 0, 1),
			]),
			'ObjectTypeExtension' => new GrammarProcessing\Vocabulary\OneOf([
				new GrammarProcessing\Vocabulary\Sequence([
					new GrammarProcessing\Vocabulary\Literal('extend'),
					new GrammarProcessing\Vocabulary\Literal('type'),
					new GrammarProcessing\Vocabulary\Nonterminal('Name'),
					new GrammarProcessing\Vocabulary\Repeat(new GrammarProcessing\Vocabulary\Nonterminal('ImplementsInterfaces'), 0, 1),
					new GrammarProcessing\Vocabulary\Repeat(new GrammarProcessing\Vocabulary\Nonterminal('Directives'), 0, 1),
					new GrammarProcessing\Vocabulary\Nonterminal('FieldsDefinition'),
				]),
				new GrammarProcessing\Vocabulary\Sequence([
					new GrammarProcessing\Vocabulary\Literal('extend'),
					new GrammarProcessing\Vocabulary\Literal('type'),
					new GrammarProcessing\Vocabulary\Nonterminal('Name'),
					new GrammarProcessing\Vocabulary\Repeat(new GrammarProcessing\Vocabulary\Nonterminal('ImplementsInterfaces'), 0, 1),
					new GrammarProcessing\Vocabulary\Nonterminal('Directives'),
				]),
				new GrammarProcessing\Vocabulary\Sequence([
					new GrammarProcessing\Vocabulary\Literal('extend'),
					new GrammarProcessing\Vocabulary\Literal('type'),
					new GrammarProcessing\Vocabulary\Nonterminal('Name'),
					new GrammarProcessing\Vocabulary\Nonterminal('ImplementsInterfaces'),
				]),
			]),
			'ImplementsInterfaces' => /*new GrammarProcessing\Vocabulary\OneOf([
				new GrammarProcessing\Vocabulary\Sequence([
					new GrammarProcessing\Vocabulary\Nonterminal('ImplementsInterfaces'),
					new GrammarProcessing\Vocabulary\Literal('&'),
					new GrammarProcessing\Vocabulary\Nonterminal('NamedType'),
				]),
				new GrammarProcessing\Vocabulary\Sequence([
					new GrammarProcessing\Vocabulary\Literal('implements'),
					new GrammarProcessing\Vocabulary\Repeat(new GrammarProcessing\Vocabulary\Literal('&'), 0, 1),
					new GrammarProcessing\Vocabulary\Nonterminal('NamedType'),
				]),
			])*/new GrammarProcessing\Vocabulary\Sequence([
				new GrammarProcessing\Vocabulary\Literal('implements'),
				new GrammarProcessing\Vocabulary\Repeat(new GrammarProcessing\Vocabulary\Literal('&'), 0, 1),
				new GrammarProcessing\Vocabulary\Nonterminal('NamedType'),
				new GrammarProcessing\Vocabulary\Repeat(new GrammarProcessing\Vocabulary\Sequence([
					new GrammarProcessing\Vocabulary\Literal('&'),
					new GrammarProcessing\Vocabulary\Nonterminal('NamedType'),
				]), 0, null),
			]),
			'FieldsDefinition' => new GrammarProcessing\Vocabulary\Sequence([
				new GrammarProcessing\Vocabulary\Literal('{'),
				new GrammarProcessing\Vocabulary\Repeat(new GrammarProcessing\Vocabulary\Nonterminal('FieldDefinition'), 1, null),
				new GrammarProcessing\Vocabulary\Literal('}'),
			]),
			'FieldDefinition' => new GrammarProcessing\Vocabulary\Sequence([
				new GrammarProcessing\Vocabulary\Repeat(new GrammarProcessing\Vocabulary\Nonterminal('Description'), 0, 1),
				new GrammarProcessing\Vocabulary\Nonterminal('Name'),
				new GrammarProcessing\Vocabulary\Repeat(new GrammarProcessing\Vocabulary\Nonterminal('ArgumentsDefinition'), 0, 1),
				new GrammarProcessing\Vocabulary\Literal(':'),
				new GrammarProcessing\Vocabulary\Nonterminal('Type'),
				new GrammarProcessing\Vocabulary\Repeat(new GrammarProcessing\Vocabulary\Nonterminal('Directives'), 0, 1),
			]),
			'ArgumentsDefinition' => new GrammarProcessing\Vocabulary\Sequence([
				new GrammarProcessing\Vocabulary\Literal('('),
				new GrammarProcessing\Vocabulary\Repeat(new GrammarProcessing\Vocabulary\Nonterminal('InputValueDefinition'), 1, null),
				new GrammarProcessing\Vocabulary\Literal(')'),
			]),
			'InputValueDefinition' => new GrammarProcessing\Vocabulary\Sequence([
				new GrammarProcessing\Vocabulary\Repeat(new GrammarProcessing\Vocabulary\Nonterminal('Description'), 0, 1),
				new GrammarProcessing\Vocabulary\Nonterminal('Name'),
				new GrammarProcessing\Vocabulary\Literal(':'),
				new GrammarProcessing\Vocabulary\Nonterminal('Type'),
				new GrammarProcessing\Vocabulary\Repeat(new GrammarProcessing\Vocabulary\Nonterminal('DefaultValue'), 0, 1),
				new GrammarProcessing\Vocabulary\Repeat(new GrammarProcessing\Vocabulary\Nonterminal('Directives'), 0, 1),
			]),
			'InterfaceTypeDefinition' => new GrammarProcessing\Vocabulary\Sequence([
				new GrammarProcessing\Vocabulary\Repeat(new GrammarProcessing\Vocabulary\Nonterminal('Description'), 0, 1),
				new GrammarProcessing\Vocabulary\Literal('interface'),
				new GrammarProcessing\Vocabulary\Nonterminal('Name'),
				new GrammarProcessing\Vocabulary\Repeat(new GrammarProcessing\Vocabulary\Nonterminal('ImplementsInterfaces'), 0, 1),
				new GrammarProcessing\Vocabulary\Repeat(new GrammarProcessing\Vocabulary\Nonterminal('Directives'), 0, 1),
				new GrammarProcessing\Vocabulary\Repeat(new GrammarProcessing\Vocabulary\Nonterminal('FieldsDefinition'), 0, 1),
			]),
			'InterfaceTypeExtension' => new GrammarProcessing\Vocabulary\OneOf([
				new GrammarProcessing\Vocabulary\Sequence([
					new GrammarProcessing\Vocabulary\Literal('extend'),
					new GrammarProcessing\Vocabulary\Literal('interface'),
					new GrammarProcessing\Vocabulary\Nonterminal('Name'),
					new GrammarProcessing\Vocabulary\Repeat(new GrammarProcessing\Vocabulary\Nonterminal('ImplementsInterfaces'), 0, 1),
					new GrammarProcessing\Vocabulary\Repeat(new GrammarProcessing\Vocabulary\Nonterminal('Directives'), 0, 1),
					new GrammarProcessing\Vocabulary\Nonterminal('FieldsDefinition'),
				]),
				new GrammarProcessing\Vocabulary\Sequence([
					new GrammarProcessing\Vocabulary\Literal('extend'),
					new GrammarProcessing\Vocabulary\Literal('interface'),
					new GrammarProcessing\Vocabulary\Nonterminal('Name'),
					new GrammarProcessing\Vocabulary\Repeat(new GrammarProcessing\Vocabulary\Nonterminal('ImplementsInterfaces'), 0, 1),
					new GrammarProcessing\Vocabulary\Nonterminal('Directives'),
				]),
				new GrammarProcessing\Vocabulary\Sequence([
					new GrammarProcessing\Vocabulary\Literal('extend'),
					new GrammarProcessing\Vocabulary\Literal('interface'),
					new GrammarProcessing\Vocabulary\Nonterminal('Name'),
					new GrammarProcessing\Vocabulary\Nonterminal('ImplementsInterfaces'),
				]),
			]),
			'UnionTypeDefinition' => new GrammarProcessing\Vocabulary\Sequence([
				new GrammarProcessing\Vocabulary\Repeat(new GrammarProcessing\Vocabulary\Nonterminal('Description'), 0, 1),
				new GrammarProcessing\Vocabulary\Literal('union'),
				new GrammarProcessing\Vocabulary\Nonterminal('Name'),
				new GrammarProcessing\Vocabulary\Repeat(new GrammarProcessing\Vocabulary\Nonterminal('Directives'), 0, 1),
				new GrammarProcessing\Vocabulary\Repeat(new GrammarProcessing\Vocabulary\Nonterminal('UnionMemberTypes'), 0, 1),
			]),
			'UnionMemberTypes' => /*new GrammarProcessing\Vocabulary\OneOf([
				new GrammarProcessing\Vocabulary\Sequence([
					new GrammarProcessing\Vocabulary\Nonterminal('UnionMemberTypes'),
					new GrammarProcessing\Vocabulary\Literal('|'),
					new GrammarProcessing\Vocabulary\Nonterminal('NamedType'),
				]),
				new GrammarProcessing\Vocabulary\Sequence([
					new GrammarProcessing\Vocabulary\Literal('='),
					new GrammarProcessing\Vocabulary\Repeat(new GrammarProcessing\Vocabulary\Literal('|'), 0, 1),
					new GrammarProcessing\Vocabulary\Nonterminal('NamedType'),
				]),
			]),
			*/new GrammarProcessing\Vocabulary\Sequence([
				new GrammarProcessing\Vocabulary\Literal('='),
				new GrammarProcessing\Vocabulary\Repeat(new GrammarProcessing\Vocabulary\Literal('|'), 0, 1),
				new GrammarProcessing\Vocabulary\Nonterminal('NamedType'),
				new GrammarProcessing\Vocabulary\Repeat(new GrammarProcessing\Vocabulary\Sequence([
					new GrammarProcessing\Vocabulary\Literal('|'),
					new GrammarProcessing\Vocabulary\Nonterminal('NamedType'),
				]), 0, null),
			]),
			'UnionTypeExtension' => new GrammarProcessing\Vocabulary\OneOf([
				new GrammarProcessing\Vocabulary\Sequence([
					new GrammarProcessing\Vocabulary\Literal('extend'),
					new GrammarProcessing\Vocabulary\Literal('union'),
					new GrammarProcessing\Vocabulary\Nonterminal('Name'),
					new GrammarProcessing\Vocabulary\Repeat(new GrammarProcessing\Vocabulary\Nonterminal('Directives'), 0, 1),
					new GrammarProcessing\Vocabulary\Nonterminal('UnionMemberTypes'),
				]),
				new GrammarProcessing\Vocabulary\Sequence([
					new GrammarProcessing\Vocabulary\Literal('extend'),
					new GrammarProcessing\Vocabulary\Literal('union'),
					new GrammarProcessing\Vocabulary\Nonterminal('Name'),
					new GrammarProcessing\Vocabulary\Nonterminal('Directives'),
				]),
			]),
			'EnumTypeDefinition' => new GrammarProcessing\Vocabulary\Sequence([
				new GrammarProcessing\Vocabulary\Repeat(new GrammarProcessing\Vocabulary\Nonterminal('Description'), 0, 1),
				new GrammarProcessing\Vocabulary\Literal('enum'),
				new GrammarProcessing\Vocabulary\Nonterminal('Name'),
				new GrammarProcessing\Vocabulary\Repeat(new GrammarProcessing\Vocabulary\Nonterminal('Directives'), 0, 1),
				new GrammarProcessing\Vocabulary\Repeat(new GrammarProcessing\Vocabulary\Nonterminal('EnumValuesDefinition'), 0, 1),
			]),
			'EnumValuesDefinition' => new GrammarProcessing\Vocabulary\Sequence([
				new GrammarProcessing\Vocabulary\Literal('{'),
				new GrammarProcessing\Vocabulary\Repeat(new GrammarProcessing\Vocabulary\Nonterminal('EnumValueDefinition'), 1, null),
				new GrammarProcessing\Vocabulary\Literal('}'),
			]),
			'EnumValueDefinition' => new GrammarProcessing\Vocabulary\Sequence([
				new GrammarProcessing\Vocabulary\Repeat(new GrammarProcessing\Vocabulary\Nonterminal('Description'), 0, 1),
				new GrammarProcessing\Vocabulary\Nonterminal('EnumValue'),
				new GrammarProcessing\Vocabulary\Repeat(new GrammarProcessing\Vocabulary\Nonterminal('Directives'), 0, 1),
			]),
			'EnumTypeExtension' => new GrammarProcessing\Vocabulary\OneOf([
				new GrammarProcessing\Vocabulary\Sequence([
					new GrammarProcessing\Vocabulary\Literal('extend'),
					new GrammarProcessing\Vocabulary\Literal('enum'),
					new GrammarProcessing\Vocabulary\Nonterminal('Name'),
					new GrammarProcessing\Vocabulary\Repeat(new GrammarProcessing\Vocabulary\Nonterminal('Directives'), 0, 1),
					new GrammarProcessing\Vocabulary\Nonterminal('EnumValuesDefinition'),
				]),
				new GrammarProcessing\Vocabulary\Sequence([
					new GrammarProcessing\Vocabulary\Literal('extend'),
					new GrammarProcessing\Vocabulary\Literal('enum'),
					new GrammarProcessing\Vocabulary\Nonterminal('Name'),
					new GrammarProcessing\Vocabulary\Nonterminal('Directives'),
				]),
			]),
			'InputObjectTypeDefinition' => new GrammarProcessing\Vocabulary\Sequence([
				new GrammarProcessing\Vocabulary\Repeat(new GrammarProcessing\Vocabulary\Nonterminal('Description'), 0, 1),
				new GrammarProcessing\Vocabulary\Literal('input'),
				new GrammarProcessing\Vocabulary\Nonterminal('Name'),
				new GrammarProcessing\Vocabulary\Repeat(new GrammarProcessing\Vocabulary\Nonterminal('Directives'), 0, 1),
				new GrammarProcessing\Vocabulary\Repeat(new GrammarProcessing\Vocabulary\Nonterminal('InputFieldsDefinition'), 0, 1),
			]),
			'InputFieldsDefinition' => new GrammarProcessing\Vocabulary\Sequence([
				new GrammarProcessing\Vocabulary\Literal('{'),
				new GrammarProcessing\Vocabulary\Repeat(new GrammarProcessing\Vocabulary\Nonterminal('InputValueDefinition'), 1, null),
				new GrammarProcessing\Vocabulary\Literal('}'),
			]),
			'InputObjectTypeExtension' => new GrammarProcessing\Vocabulary\OneOf([
				new GrammarProcessing\Vocabulary\Sequence([
					new GrammarProcessing\Vocabulary\Literal('extend'),
					new GrammarProcessing\Vocabulary\Literal('input'),
					new GrammarProcessing\Vocabulary\Nonterminal('Name'),
					new GrammarProcessing\Vocabulary\Repeat(new GrammarProcessing\Vocabulary\Nonterminal('Directives'), 0, 1),
					new GrammarProcessing\Vocabulary\Nonterminal('InputFieldsDefinition'),
				]),
				new GrammarProcessing\Vocabulary\Sequence([
					new GrammarProcessing\Vocabulary\Literal('extend'),
					new GrammarProcessing\Vocabulary\Literal('input'),
					new GrammarProcessing\Vocabulary\Nonterminal('Name'),
					new GrammarProcessing\Vocabulary\Nonterminal('Directives'),
				]),
			]),
			'DirectiveDefinition' => new GrammarProcessing\Vocabulary\Sequence([
				new GrammarProcessing\Vocabulary\Repeat(new GrammarProcessing\Vocabulary\Nonterminal('Description'), 0, 1),
				new GrammarProcessing\Vocabulary\Literal('directive'),
				new GrammarProcessing\Vocabulary\Literal('@'),
				new GrammarProcessing\Vocabulary\Nonterminal('Name'),
				new GrammarProcessing\Vocabulary\Repeat(new GrammarProcessing\Vocabulary\Nonterminal('ArgumentsDefinition'), 0, 1),
				new GrammarProcessing\Vocabulary\Repeat(new GrammarProcessing\Vocabulary\Literal('repeatable'), 0, 1),
				new GrammarProcessing\Vocabulary\Literal('on'),
				new GrammarProcessing\Vocabulary\Nonterminal('DirectiveLocations'),
			]),
			'DirectiveLocations' => /*new GrammarProcessing\Vocabulary\OneOf([
				new GrammarProcessing\Vocabulary\Sequence([
					new GrammarProcessing\Vocabulary\Nonterminal('DirectiveLocations'),
					new GrammarProcessing\Vocabulary\Literal('|'),
					new GrammarProcessing\Vocabulary\Nonterminal('DirectiveLocation'),
				]),
				new GrammarProcessing\Vocabulary\Sequence([
					new GrammarProcessing\Vocabulary\Repeat(new GrammarProcessing\Vocabulary\Literal('|'), 0, 1),
					new GrammarProcessing\Vocabulary\Nonterminal('DirectiveLocation'),
				]),
			])*/new GrammarProcessing\Vocabulary\Sequence([
				new GrammarProcessing\Vocabulary\Repeat(new GrammarProcessing\Vocabulary\Literal('|'), 0, 1),
				new GrammarProcessing\Vocabulary\Nonterminal('DirectiveLocation'),
				new GrammarProcessing\Vocabulary\Repeat(new GrammarProcessing\Vocabulary\Sequence([
					new GrammarProcessing\Vocabulary\Literal('|'),
					new GrammarProcessing\Vocabulary\Nonterminal('DirectiveLocation'),
				]), 0, null),
			]),
			'DirectiveLocation' => new GrammarProcessing\Vocabulary\OneOf([
				new GrammarProcessing\Vocabulary\Nonterminal('ExecutableDirectiveLocation'),
				new GrammarProcessing\Vocabulary\Nonterminal('TypeSystemDirectiveLocation'),
			]),
			'ExecutableDirectiveLocation' => new GrammarProcessing\Vocabulary\OneOf([
				new GrammarProcessing\Vocabulary\Literal('QUERY'),
				new GrammarProcessing\Vocabulary\Literal('MUTATION'),
				new GrammarProcessing\Vocabulary\Literal('SUBSCRIPTION'),
				new GrammarProcessing\Vocabulary\Literal('FIELD'),
				new GrammarProcessing\Vocabulary\Literal('FRAGMENT_DEFINITION'),
				new GrammarProcessing\Vocabulary\Literal('FRAGMENT_SPREAD'),
				new GrammarProcessing\Vocabulary\Literal('INLINE_FRAGMENT'),
			]),
			'TypeSystemDirectiveLocation' => new GrammarProcessing\Vocabulary\OneOf([
				new GrammarProcessing\Vocabulary\Literal('SCHEMA'),
				new GrammarProcessing\Vocabulary\Literal('SCALAR'),
				new GrammarProcessing\Vocabulary\Literal('OBJECT'),
				new GrammarProcessing\Vocabulary\Literal('FIELD_DEFINITION'),
				new GrammarProcessing\Vocabulary\Literal('ARGUMENT_DEFINITION'),
				new GrammarProcessing\Vocabulary\Literal('INTERFACE'),
				new GrammarProcessing\Vocabulary\Literal('UNION'),
				new GrammarProcessing\Vocabulary\Literal('ENUM'),
				new GrammarProcessing\Vocabulary\Literal('ENUM_VALUE'),
				new GrammarProcessing\Vocabulary\Literal('INPUT_OBJECT'),
				new GrammarProcessing\Vocabulary\Literal('INPUT_FIELD_DEFINITION'),
			]),
		];

		return new GrammarProcessing\Grammar(
			ignoredTokenSymbols: $ignoredTokenSymbols,
			lexicalSymbols: $lexicalSymbols,
			syntacticSymbols: $syntacticSymbols,
			syntaxTokenSymbols: $syntaxTokenSymbols,
		);
	}

}
