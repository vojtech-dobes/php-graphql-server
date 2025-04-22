<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\Language\Nodes;

use Generator;
use Vojtechdobes\GrammarProcessing;
use Vojtechdobes\GraphQL;


final class InputValueDefinition implements GrammarProcessing\NodeInterpretation
{

	public function interpret(GrammarProcessing\Node $node): Generator
	{
		return new GraphQL\TypeSystem\InputValueDefinition(
			defaultValue: (yield $node->value[4]->value[0] ?? null) ?? null,
			description: yield $node->value[0]->value[0] ?? null,
			directives: (yield $node->value[5]->value[0] ?? null) ?? [],
			name: yield $node->value[1],
			type: yield $node->value[3],
		);
	}

}
