<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL;


final class ServiceFactory
{

	public static function createService(ExecutableSchema $executableSchema): Service
	{
		$executableDocumentParser = new ExecutableDocumentParser();
		$requestExecutor = new RequestExecutor();

		return new Service(
			executableDocumentParser: $executableDocumentParser,
			executableSchema: $executableSchema,
			requestExecutor: $requestExecutor,
			schema: $executableSchema->schema,
		);
	}

}
