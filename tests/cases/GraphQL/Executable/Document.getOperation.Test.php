<?php declare(strict_types=1);

require_once __DIR__ . '/../../../bootstrap.php';


$schema = new Vojtechdobes\GraphQL\SchemaParser()
	->parseSchema('type Query { a: String }', [], [])
	->buildSchema();


$document = new Vojtechdobes\GraphQL\ExecutableDocumentParser()
	->parseExecutableDocument($schema, 'query Q { a }')
	->buildExecutableDocument();

Tester\Assert::type(
	Vojtechdobes\GraphQL\Executable\Operation::class,
	$document->getOperation('Q'),
);

Tester\Assert::exception(
	static fn () => $document->getOperation('P'),
	Vojtechdobes\GraphQL\Exceptions\UnknownOperationException::class,
);
