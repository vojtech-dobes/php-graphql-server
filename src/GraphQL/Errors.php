<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL;


final class Errors
{

	/** @var list<Error> */
	public private(set) array $errors = [];



	public function addErrorMessage(string $message): void
	{
		$this->errors[] = new Error($message);
	}



	public function addError(Error $error): void
	{
		$this->errors[] = $error;
	}

}
