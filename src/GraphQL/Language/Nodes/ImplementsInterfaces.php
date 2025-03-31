<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\Language\Nodes;

use Generator;
use Vojtechdobes\GrammarProcessing;
use Vojtechdobes\GraphQL;


final class ImplementsInterfaces implements GrammarProcessing\NodeInterpretation
{

	public function interpret(GrammarProcessing\Node $node): Generator
	{
		$result = [
			(yield $node->value[2])->name,
		];

		foreach ($node->value[3]->value as $subnode) {
			$result[] = (yield $subnode->value[1])->name;
		}

		return $result;
	}

}
