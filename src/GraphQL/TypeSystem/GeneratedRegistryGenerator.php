<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\TypeSystem;

use Nette;
use ReflectionClass;
use ReflectionParameter;
use UnitEnum;


final class GeneratedRegistryGenerator
{

	/** @var array<string, list<ReflectionParameter>> */
	private array $parametersPerClass = [];



	/**
	 * @param array<string, object> $elements
	 */
	public function generateRegistry(
		Nette\PhpGenerator\ClassType $class,
		array $elements,
	): void
	{
		$class
			->setFinal()
			->setExtends(GeneratedRegistry::class);

		foreach ($elements as $elementName => $element) {
			$class->addMethod(GeneratedRegistry::MethodPrefix . $elementName)
				->setProtected()
				->setReturnType($element::class)
				->setBody('return ?;', [$this->dumpObject($element)]);
		}
	}



	private function dump(mixed $value): mixed
	{
		return match (true) {
			is_array($value) => array_map(
				fn ($item) => $this->dump($item),
				$value,
			),
			is_object($value) && !$value instanceof UnitEnum => $this->dumpObject($value),
			default => $value,
		};
	}



	private function dumpObject(object $object): Nette\PhpGenerator\Literal
	{
		$this->parametersPerClass[$object::class] ??= new ReflectionClass($object::class)
			->getMethod('__construct')
			->getParameters();

		$parameters = [];

		foreach ($this->parametersPerClass[$object::class] as $constructorParameter) {
			$parameters[$constructorParameter->name] = $this->dump($object->{$constructorParameter->name});
		}

		return Nette\PhpGenerator\Literal::new(
			$object::class,
			$parameters,
		);
	}

}
