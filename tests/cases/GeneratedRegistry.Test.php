<?php declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';


$registry = new class extends Vojtechdobes\GraphQL\TypeSystem\GeneratedRegistry {

	protected function create__a(): mixed
	{
		return (object) ['field' => 'Alice'];
	}

	protected function create__b(): mixed
	{
		return (object) ['field' => 'Bob'];
	}

};


$a = $registry->getItem('a');

Tester\Assert::type(stdClass::class, $a);
Tester\Assert::same('Alice', $a->field);
Tester\Assert::same($a, $registry->getItem('a'));
Tester\Assert::same($a, $registry->getItemOrNull('a'));

$b = $registry->getItem('b');

Tester\Assert::type(stdClass::class, $b);
Tester\Assert::same('Bob', $b->field);
Tester\Assert::same($b, $registry->getItem('b'));
Tester\Assert::same($b, $registry->getItemOrNull('b'));

$c = $registry->getItemOrNull('c');

Tester\Assert::null($c);

Tester\Assert::exception(
	static fn () => $registry->getItem('c'),
	LogicException::class,
);
