<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\Language\Nodes;

use Generator;
use Vojtechdobes\GrammarProcessing;


final class EnumValuesDefinition implements GrammarProcessing\NodeInterpretation
{

	public function interpret(GrammarProcessing\Node $node): Generator
	{
		$result = [];

		foreach ($node->value[1]->value as $enumValueDefinitionNode) {
			$result[] = yield $enumValueDefinitionNode;
		}

		return $result;
	}

}
