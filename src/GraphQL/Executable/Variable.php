<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\Executable;


final class Variable
{

	public function __construct(
		public readonly string $name,
	) {}

}
