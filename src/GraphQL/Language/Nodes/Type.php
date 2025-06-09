<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\Language\Nodes;

use Generator;
use Vojtechdobes\GrammarProcessing;


final class Type implements GrammarProcessing\NodeInterpretation
{

	public function interpret(GrammarProcessing\Node $node): Generator
	{
		return yield $node;
	}

}
