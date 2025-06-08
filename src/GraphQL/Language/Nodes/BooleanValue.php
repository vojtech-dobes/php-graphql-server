<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\Language\Nodes;

use Generator;
use Vojtechdobes\GrammarProcessing;
use Vojtechdobes\GraphQL;


final class BooleanValue implements GrammarProcessing\NodeInterpretation
{

	public function interpret(GrammarProcessing\Node $node): Generator
	{
		return new GraphQL\Values\BooleanValue(
			// @phpstan-ignore match.unhandled ($node is already guaranteed to be only "true" or "false")
			match (yield $node) {
				'true' => true,
				'false' => false,
			},
		);
	}

}
