<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL;


final class Error
{

	/**
	 * @param array<string, mixed>|null $extensions
	 */
	public function __construct(
		public readonly string $message,
		private readonly ?Execution\ResponsePath $responsePath = null,
		private readonly ?array $extensions = null,
	) {}



	public function withResponsePath(Execution\ResponsePath $responsePath): self
	{
		return new self(
			$this->message,
			$responsePath,
		);
	}



	/**
	 * @return array{
	 *   message: string,
	 *   path?: list<int|string>,
	 * }
	 */
	public function toResponse(): array
	{
		$result = [
			'message' => $this->message,
		];

		if ($this->responsePath !== null) {
			$result['path'] = $this->responsePath->getPath();
		}

		if ($this->extensions !== null && $this->extensions !== []) {
			$result['extensions'] = $this->extensions;
		}

		return $result;
	}

}
