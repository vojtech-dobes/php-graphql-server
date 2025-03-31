<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\Language\Nodes;

use Generator;
use Vojtechdobes\GrammarProcessing;
use Vojtechdobes\GraphQL;


final class EnumTypeDefinition implements GrammarProcessing\NodeInterpretation
{

	public function interpret(GrammarProcessing\Node $node): Generator
	{
		return new GraphQL\TypeSystem\EnumTypeDefinition(
			description: yield $node->value[0]->value[0] ?? null,
			directives: (yield $node->value[3]->value[0] ?? null) ?? [],
			enumValues: (yield $node->value[4]->value[0] ?? null) ?? [],
			name: yield $node->value[2],
		);
	}

}
