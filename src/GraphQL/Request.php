<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL;


final class Request
{

	/**
	 * @param array<string, mixed> $variableValues
	 */
	public function __construct(
		public readonly Executable\Document $document,
		public readonly array $variableValues,
		public readonly ?bool $enableIntrospection = null,
	) {}

}
