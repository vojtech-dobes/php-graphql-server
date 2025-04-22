<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\Language\Nodes;

use Generator;
use LogicException;
use Vojtechdobes\GrammarProcessing;
use Vojtechdobes\GraphQL;


final class InterfaceTypeExtension implements GrammarProcessing\NodeInterpretation
{

	public function interpret(GrammarProcessing\Node $node): Generator
	{
		return new GraphQL\TypeSystem\TypeExtension(
			new GraphQL\TypeSystem\InterfaceTypeDefinition(
				description: null,
				directives: match (count($node->value)) {
					6 => (yield $node->value[4]->value[0] ?? null) ?? [],
					5 => yield $node->value[4],
					4 => [],
					default => throw new LogicException("This can't happen"),
				},
				fields: match (count($node->value)) {
					6 => yield $node->value[5],
					5 => [],
					4 => [],
					default => throw new LogicException("This can't happen"),
				},
				implementedInterfaces: match (count($node->value)) {
					6 => (yield $node->value[3]->value[0] ?? null) ?? [],
					5 => (yield $node->value[3]->value[0] ?? null) ?? [],
					4 => yield $node->value[3],
					default => throw new LogicException("This can't happen"),
				},
				name: yield $node->value[2],
			),
		);
	}

}
