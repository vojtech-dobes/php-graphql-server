<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\Language\Nodes;

use Generator;
use Vojtechdobes\GrammarProcessing;
use Vojtechdobes\GraphQL;


final class ListValue implements GrammarProcessing\NodeInterpretation
{

	public function interpret(GrammarProcessing\Node $node): Generator
	{
		$items = [];

		if (count($node->value) === 3) {
			foreach ($node->value[1]->value as $itemValue) {
				$items[] = yield $itemValue;
			}
		}

		return new GraphQL\Values\ListValue($items);
	}

}
