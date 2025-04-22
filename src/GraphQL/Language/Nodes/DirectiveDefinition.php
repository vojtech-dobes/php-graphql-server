<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\Language\Nodes;

use Generator;
use Vojtechdobes\GrammarProcessing;
use Vojtechdobes\GraphQL;


final class DirectiveDefinition implements GrammarProcessing\NodeInterpretation
{

	public function interpret(GrammarProcessing\Node $node): Generator
	{
		return new GraphQL\TypeSystem\DirectiveDefinition(
			argumentDefinitions: (yield $node->value[4]->value[0] ?? null) ?? [],
			description: yield $node->value[0]->value[0] ?? null,
			isRepeatable: $node->value[5]->value !== [],
			locations: yield $node->value[7],
			name: yield $node->value[3],
		);
	}

}
