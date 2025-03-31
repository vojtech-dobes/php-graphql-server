<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\Language\Nodes;

use Generator;
use Vojtechdobes\GrammarProcessing;
use Vojtechdobes\GraphQL;


final class SchemaDefinition implements GrammarProcessing\NodeInterpretation
{

	public function interpret(GrammarProcessing\Node $node): Generator
	{
		$types = [];

		foreach ($node->value[4]->value as $rootOperationTypeDefinition) {
			$types[] = yield $rootOperationTypeDefinition;
		}

		return new GraphQL\TypeSystem\Builder\RootOperationTypes(
			description: yield $node->value[0]->value[0] ?? null,
			directives: (yield $node->value[2]->value[0] ?? null) ?? [],
			types: $types,
		);
	}

}
