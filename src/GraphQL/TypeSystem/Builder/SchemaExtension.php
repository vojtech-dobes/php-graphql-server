<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\TypeSystem\Builder;


final class SchemaExtension
{

	public function __construct(
		public readonly RootOperationTypes $rootOperationTypes,
	) {}

}
