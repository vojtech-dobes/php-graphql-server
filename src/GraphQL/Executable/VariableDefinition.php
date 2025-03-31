<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\Executable;

use LogicException;
use Vojtechdobes\GraphQL;


final class VariableDefinition
{

	/**
	 * @param GraphQL\Values\Value<mixed>|Variable|null $defaultValue
	 */
	public function __construct(
		public readonly string $name,
		public readonly GraphQL\Types\Type $type,
		public readonly GraphQL\Values\Value|Variable|null $defaultValue,
	) {}



	public function getDefaultRuntimeValue(): mixed
	{
		if (
			$this->defaultValue === null
			|| $this->defaultValue instanceof Variable
		) {
			throw new LogicException(
				sprintf(
					"%s::%s() can't be called if default value isn't defined or is Variable",
					self::class,
					__METHOD__,
				),
			);
		}

		return $this->defaultValue->getRuntimeValue();
	}

}
