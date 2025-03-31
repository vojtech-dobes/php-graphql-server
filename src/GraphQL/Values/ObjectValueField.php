<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\Values;


final class ObjectValueField
{

	/**
	 * @param Value<mixed> $value
	 */
	public function __construct(
		public readonly string $name,
		public readonly Value $value,
	) {}

}
