<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\Language\Nodes;

use Generator;
use Vojtechdobes\GrammarProcessing;
use Vojtechdobes\GraphQL;


final class ObjectValue implements GrammarProcessing\NodeInterpretation
{

	public function interpret(GrammarProcessing\Node $node): Generator
	{
		$fields = [];

		if (count($node->value) === 3) {
			foreach ($node->value[1]->value as $objectField) {
				$fields[] = yield $objectField;
			}
		}

		return new GraphQL\Values\ObjectValue($fields);
	}

}
