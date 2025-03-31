<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\Language\Nodes;

use Generator;
use Vojtechdobes\GrammarProcessing;


final class ArgumentsDefinition implements GrammarProcessing\NodeInterpretation
{

	public function interpret(GrammarProcessing\Node $node): Generator
	{
		$result = [];

		foreach ($node->value[1]->value as $inputValueDefinitionNode) {
			$result[] = yield $inputValueDefinitionNode;
		}

		return $result;
	}

}
