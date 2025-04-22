<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\Executable;

use Vojtechdobes\GraphQL;


final class Document
{

	/**
	 * @param array<string, FragmentDefinition> $fragmentDefinitions
	 * @param non-empty-list<OperationDefinition> $operationDefinitions
	 * @param list<GraphQL\TypeSystem\ObjectTypeDefinition> $operationRootTypeDefinitions
	 */
	public function __construct(
		public readonly array $fragmentDefinitions,
		public readonly array $operationDefinitions,
		private readonly array $operationRootTypeDefinitions,
	) {}



	/**
	 * @throws GraphQL\Exceptions\UnknownOperationException
	 */
	public function getOperation(?string $operationName): Operation
	{
		foreach ($this->operationDefinitions as $i => $operationDefinition) {
			if ($operationDefinition->name === $operationName) {
				return new Operation(
					definition: $operationDefinition,
					rootType: $this->operationRootTypeDefinitions[$i],
				);
			}
		}

		throw new GraphQL\Exceptions\UnknownOperationException(
			message: sprintf(
				"%s can't be found in provided document",
				$operationName !== null
					? "Operation '{$operationName}'"
					: 'Anonymous operation',
			),
			operationName: $operationName,
		);
	}

}
