<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL;

use GuzzleHttp;


final class Service
{

	public function __construct(
		private readonly ExecutableDocumentParser $executableDocumentParser,
		private readonly ExecutableSchema $executableSchema,
		private readonly RequestExecutor $requestExecutor,
		private readonly TypeSystem\Schema $schema,
	) {}



	public function parseExecutableDocument(string $documentText): Executable\Document
	{
		return $this->executableDocumentParser
			->parseExecutableDocument($this->schema, $documentText)
			->buildExecutableDocument();
	}



	public function executeRequest(Request $request): GuzzleHttp\Promise\PromiseInterface
	{
		return $this->requestExecutor->executeRequest(
			$this->executableSchema,
			$request,
		);
	}



	/**
	 * @param array<Request> $requests
	 */
	public function executeRequestBatch(array $requests): GuzzleHttp\Promise\PromiseInterface
	{
		return GuzzleHttp\Promise\Utils::all(
			array_map(
				fn ($request) => $this->executeRequest($request),
				$requests,
			),
		);
	}

}
