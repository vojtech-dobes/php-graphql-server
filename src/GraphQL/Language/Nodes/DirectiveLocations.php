<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\Language\Nodes;

use Generator;
use Vojtechdobes\GrammarProcessing;
use Vojtechdobes\GraphQL;


final class DirectiveLocations implements GrammarProcessing\NodeInterpretation
{

	public function interpret(GrammarProcessing\Node $node): Generator
	{
		$result = [
			yield $node->value[1],
		];

		foreach ($node->value[2]->value as $subnode) {
			$result[] = yield $subnode->value[1];
		}

		return $result;
	}

}
