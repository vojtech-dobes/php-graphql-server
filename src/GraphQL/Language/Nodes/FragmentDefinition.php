<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\Language\Nodes;

use Generator;
use Vojtechdobes\GrammarProcessing;
use Vojtechdobes\GraphQL;


final class FragmentDefinition implements GrammarProcessing\NodeInterpretation
{

	public function interpret(GrammarProcessing\Node $node): Generator
	{
		return new GraphQL\Executable\FragmentDefinition(
			directives: (yield $node->value[3]->value[0] ?? null) ?? [],
			name: yield $node->value[1],
			onType: yield $node->value[2],
			selectionSet: yield $node->value[4],
		);
	}

}
