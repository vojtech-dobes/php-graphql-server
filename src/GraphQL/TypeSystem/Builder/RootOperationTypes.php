<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\TypeSystem\Builder;

use Vojtechdobes\GraphQL;


final class RootOperationTypes
{

	/**
	 * @param list<GraphQL\Executable\Directive<GraphQL\Values\Value<mixed>>> $directives
	 * @param list<
	 *   array{
	 *     operationType: GraphQL\OperationType,
	 *     type: string,
	 *   },
	 * > $types
	 */
	public function __construct(
		public readonly ?string $description,
		public readonly array $directives,
		public readonly array $types,
	) {}

}
