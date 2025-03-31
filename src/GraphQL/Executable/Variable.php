<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\Executable;

use Vojtechdobes\GraphQL;


final class Variable
{

	public function __construct(
		public readonly string $name,
	) {}

}
