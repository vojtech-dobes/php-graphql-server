<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\Language\Nodes;

use Generator;
use Vojtechdobes\GrammarProcessing;


final class Arguments implements GrammarProcessing\NodeInterpretation
{

	public function interpret(GrammarProcessing\Node $node): Generator
	{
		$result = [];

		foreach ($node->value[1]->value as $argumentNode) {
			$result[] = yield $argumentNode;
		}

		return $result;
	}

}
