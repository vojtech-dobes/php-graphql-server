<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\Language\Nodes;

use Generator;
use Vojtechdobes\GrammarProcessing;
use Vojtechdobes\GraphQL;


final class InlineFragment implements GrammarProcessing\NodeInterpretation
{

	public function interpret(GrammarProcessing\Node $node): Generator
	{
		return new GraphQL\Executable\InlineFragment(
			directives: (yield $node->value[2]->value[0] ?? null) ?? [],
			onType: yield $node->value[1]->value[0] ?? null,
			selectionSet: yield $node->value[3],
		);
	}

}
