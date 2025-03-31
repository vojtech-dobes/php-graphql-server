<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\Language\Nodes;

use Generator;
use Vojtechdobes\GrammarProcessing;
use Vojtechdobes\GraphQL;


final class OperationDefinition implements GrammarProcessing\NodeInterpretation
{

	public function interpret(GrammarProcessing\Node $node): Generator
	{
		if ($node instanceof GrammarProcessing\NonterminalNode) {
			return new GraphQL\Executable\OperationDefinition(
				directives: [],
				name: null,
				selectionSet: yield $node,
				type: GraphQL\OperationType::Query,
				variableDefinitions: [],
			);
		}

		return new GraphQL\Executable\OperationDefinition(
			directives: (yield $node->value[3]->value[0] ?? null) ?? [],
			name: yield $node->value[1]->value[0] ?? null,
			selectionSet: yield $node->value[4],
			type: yield $node->value[0],
			variableDefinitions: (yield $node->value[2]->value[0] ?? null) ?? [],
		);
	}

}
