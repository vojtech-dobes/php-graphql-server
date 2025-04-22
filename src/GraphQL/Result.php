<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL;


final class Result
{

	/**
	 * @param array<string, mixed>|object{}|null $data
	 * @param list<Error> $errors
	 * @param array<string, mixed> $extensions
	 */
	private function __construct(
		private readonly array|object|null $data,
		private readonly array $errors,
		private readonly bool $isExecuted,
		private readonly array $extensions,
	) {}



	/**
	 * @param array<string, mixed>|object{}|null $data
	 * @param list<Error> $errors
	 */
	public static function createFromExecution(
		array|object|null $data,
		array $errors,
	): self
	{
		return new self(
			data: $data,
			errors: $errors,
			isExecuted: true,
			extensions: [],
		);
	}



	public static function createFromRequestError(Error $error): self
	{
		return self::createFromRequestErrors([$error]);
	}



	/**
	 * @param non-empty-list<Error> $errors
	 */
	public static function createFromRequestErrors(array $errors): self
	{
		return new self(
			data: null,
			errors: $errors,
			isExecuted: false,
			extensions: [],
		);
	}



	/**
	 * @return array{
	 *   data?: array<string, mixed>|object{}|null,
	 *   errors?: non-empty-list<
	 *     array{
	 *       message: string,
	 *     },
	 *   >,
	 *   extensions?: non-empty-array<string, mixed>,
	 * }
	 */
	public function toResponse(): array
	{
		$result = [];

		if ($this->isExecuted) {
			$result['data'] = $this->data;
		}

		if ($this->errors !== []) {
			$result['errors'] = array_map(
				static fn ($error) => $error->toResponse(),
				$this->errors,
			);
		}

		if ($this->extensions !== []) {
			$result['extensions'] = $this->extensions;
		}

		return $result;
	}

}
