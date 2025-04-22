<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\Language\Nodes;

use Generator;
use Vojtechdobes\GrammarProcessing;
use Vojtechdobes\GraphQL;


final class ScalarTypeExtension implements GrammarProcessing\NodeInterpretation
{

	public function interpret(GrammarProcessing\Node $node): Generator
	{
		return new GraphQL\TypeSystem\TypeExtension(
			new GraphQL\TypeSystem\ScalarTypeDefinition(
				description: null,
				directives: yield $node->value[3],
				name: yield $node->value[2],
			),
		);
	}

}
