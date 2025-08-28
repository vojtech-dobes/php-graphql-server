<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\TypeSystem;

use LogicException;
use ReflectionClass;
use ReflectionMethod;


/**
 * @template TItem
 * @implements Registry<TItem>
 */
abstract class GeneratedRegistry implements Registry
{

	public const string MethodPrefix = 'create__';

	/** @var array<TItem>|null */
	private ?array $all = null;

	/** @var array<string, TItem|null> */
	private array $cache = [];



	public function getItem(string $name): mixed
	{
		return $this->getItemOrNull($name) ?? throw new LogicException(
			"Item '{$name}' can't be null",
		);
	}



	public function getItemOrNull(string $name): mixed
	{
		if (array_key_exists($name, $this->cache) === false) {
			$methodName = self::MethodPrefix . $name;

			if (method_exists($this, $methodName)) {
				$this->cache[$name] = $this->{$methodName}();
			} else {
				$this->cache[$name] = null;
			}
		}

		return $this->cache[$name];
	}



	public function getAll(): array
	{
		if ($this->all === null) {
			$prefixLength = strlen(self::MethodPrefix);

			$this->all = [];

			foreach (new ReflectionClass(static::class)->getMethods(ReflectionMethod::IS_PROTECTED) as $method) {
				$name = substr($method->name, $prefixLength);
				$this->all[$name] = $this->getItem($name);
			}
		}

		return $this->all;
	}

}
