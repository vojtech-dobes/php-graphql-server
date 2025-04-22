<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\Language\Nodes;

use Generator;
use LogicException;
use Vojtechdobes\GrammarProcessing;
use Vojtechdobes\GraphQL;


final class SchemaExtension implements GrammarProcessing\NodeInterpretation
{

	public function interpret(GrammarProcessing\Node $node): Generator
	{
		$types = [];

		if (count($node->value) === 6) {
			foreach ($node->value[4]->value as $rootOperationTypeDefinition) {
				$types[] = yield $rootOperationTypeDefinition;
			}
		}

		return new GraphQL\TypeSystem\Builder\SchemaExtension(
			new GraphQL\TypeSystem\Builder\RootOperationTypes(
				description: null,
				directives: match (count($node->value)) {
					6 => (yield $node->value[2]->value[0] ?? null) ?? [],
					3 => yield $node->value[2],
					default => throw new LogicException("This can't happen"),
				},
				types: $types,
			),
		);
	}

}
