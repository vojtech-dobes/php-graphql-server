<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\Language\Nodes;

use Generator;
use LogicException;
use Vojtechdobes\GrammarProcessing;
use Vojtechdobes\GraphQL;


final class InputObjectTypeExtension implements GrammarProcessing\NodeInterpretation
{

	public function interpret(GrammarProcessing\Node $node): Generator
	{
		return new GraphQL\TypeSystem\TypeExtension(
			new GraphQL\TypeSystem\InputObjectTypeDefinition(
				description: null,
				directives: match (count($node->value)) {
					5 => (yield $node->value[3]->value[0] ?? null) ?? [],
					4 => yield $node->value[3],
					default => throw new LogicException("This can't happen"),
				},
				fields: (yield $node->value[4] ?? null) ?? [],
				name: yield $node->value[2],
			),
		);
	}

}
