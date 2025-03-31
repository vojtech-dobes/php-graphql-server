<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\Language\Nodes;

use Generator;
use Vojtechdobes\GrammarProcessing;
use Vojtechdobes\GraphQL;


final class Directive implements GrammarProcessing\NodeInterpretation
{

	public function interpret(GrammarProcessing\Node $node): Generator
	{
		return new GraphQL\Executable\Directive(
			arguments: (yield $node->value[2]->value[0] ?? null) ?? [],
			name: yield $node->value[1],
		);
	}

}
