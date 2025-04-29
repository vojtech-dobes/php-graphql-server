<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL;


final class Error
{

	/**
	 * @param array<string, mixed>|null $extensions
	 * @param list<
	 *   array{
	 *     line: int,
	 *     column: int,
	 *   },
	 * > $locations
	 */
	public function __construct(
		public readonly string $message,
		private readonly ?Execution\ResponsePath $responsePath = null,
		private readonly ?array $extensions = null,
		private readonly array $locations = [],
	) {}



	public function withLocation(int $line, int $column): self
	{
		return new self(
			$this->message,
			$this->responsePath,
			$this->extensions,
			[...$this->locations, [
				'line' => $line,
				'column' => $column,
			]],
		);
	}



	public function withResponsePath(Execution\ResponsePath $responsePath): self
	{
		return new self(
			$this->message,
			$responsePath,
			$this->extensions,
			$this->locations,
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

		if ($this->locations !== []) {
			$result['locations'] = $this->locations;
		}

		if ($this->responsePath !== null) {
			$result['path'] = $this->responsePath->getPath();
		}

		if ($this->extensions !== null && $this->extensions !== []) {
			$result['extensions'] = $this->extensions;
		}

		return $result;
	}

}
