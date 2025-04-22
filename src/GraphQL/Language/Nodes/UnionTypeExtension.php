<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\Language\Nodes;

use Generator;
use LogicException;
use Vojtechdobes\GrammarProcessing;
use Vojtechdobes\GraphQL;


final class UnionTypeExtension implements GrammarProcessing\NodeInterpretation
{

	public function interpret(GrammarProcessing\Node $node): Generator
	{
		return new GraphQL\TypeSystem\TypeExtension(
			new GraphQL\TypeSystem\UnionTypeDefinition(
				description: null,
				directives: match (count($node->value)) {
					5 => (yield $node->value[3]->value[0] ?? null) ?? [],
					4 => yield $node->value[3],
					default => throw new LogicException("This can't happen"),
				},
				name: yield $node->value[2],
				possibleTypes: (yield $node->value[4] ?? null) ?? [],
			),
		);
	}

}
