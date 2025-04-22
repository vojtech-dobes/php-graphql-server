<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL;

use GuzzleHttp;


final class RequestExecutor
{

	public function executeRequest(
		ExecutableSchema $executableSchema,
		Request $request,
	): GuzzleHttp\Promise\PromiseInterface
	{
		if (count($request->document->operationDefinitions) > 1) {
			return new GuzzleHttp\Promise\FulfilledPromise(
				Result::createFromRequestError(
					new Error("Document with multiple operations can't be executed without selected operation name"),
				),
			);
		}

		return $this->executeOperationInRequest(
			$executableSchema,
			$request,
			$request->document->operationDefinitions[0]->name,
		);
	}



	public function executeOperationInRequest(
		ExecutableSchema $executableSchema,
		Request $request,
		?string $operationName,
	): GuzzleHttp\Promise\PromiseInterface
	{
		try {
			$operation = $request->document->getOperation($operationName);
		} catch (Exceptions\UnknownOperationException $e) {
			return new GuzzleHttp\Promise\FulfilledPromise(
				Result::createFromRequestError(
					new Error($e->getMessage()),
				),
			);
		}

		try {
			$operationExecution = $executableSchema->createOperationExecution(
				$request->document,
				$operation->definition->variableDefinitions,
				$request->variableValues,
				$request->enableIntrospection,
			);
		} catch (Exceptions\CannotResolveVariableRuntimeValuesException $e) {
			return new GuzzleHttp\Promise\FulfilledPromise(
				Result::createFromRequestErrors($e->errors),
			);
		}

		$data = $operationExecution->executeSelectionSet(
			$operation->rootType,
			null,
			$operation->definition->selectionSet,
		);

		$fieldErrors = new Errors();

		return $data->then(
			static fn ($data) => Result::createFromExecution(
				data: $data->getData($fieldErrors, Execution\ResponsePath::createBasePath()),
				errors: $fieldErrors->errors,
			),
		);
	}

}
