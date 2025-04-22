<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\Language\Nodes;

use Generator;
use Vojtechdobes\GrammarProcessing;
use Vojtechdobes\GraphQL;


final class VariableDefinition implements GrammarProcessing\NodeInterpretation
{

	public function interpret(GrammarProcessing\Node $node): Generator
	{
		return new GraphQL\Executable\VariableDefinition(
			defaultValue: (yield $node->value[3]->value[0] ?? null) ?? null,
			name: (yield $node->value[0])->name,
			type: yield $node->value[2],
		);
	}

}
