<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\Language\Nodes;

use Generator;
use Vojtechdobes\GrammarProcessing;
use Vojtechdobes\GraphQL;


final class EnumValue implements GrammarProcessing\NodeInterpretation
{

	public function interpret(GrammarProcessing\Node $node): Generator
	{
		return new GraphQL\Values\EnumValue(yield $node);
	}

}
