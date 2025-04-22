<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\Language\Nodes;

use Generator;
use Vojtechdobes\GrammarProcessing;


final class Directives implements GrammarProcessing\NodeInterpretation
{

	public function interpret(GrammarProcessing\Node $node): Generator
	{
		$result = [];

		foreach ($node->value as $directiveNode) {
			$result[] = yield $directiveNode;
		}

		return $result;
	}

}
