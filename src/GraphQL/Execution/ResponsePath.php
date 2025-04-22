<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\Execution;


final class ResponsePath
{

	private function __construct(
		private readonly ?self $basePath,
		public readonly int|string $segment,
	) {}



	public static function createBasePath(): self
	{
		return new self(null, 0);
	}



	// public function addField(GraphQL\Executable\Field $field): self
	public function addField(string $field): self
	{
		return new self($this, $field);
	}



	public function addItemIndex(int $itemIndex): self
	{
		return new self($this, $itemIndex);
	}



	/**
	 * @return list<int|string>
	 */
	public function getPath(): array
	{
		if ($this->basePath === null) {
			return [];
		}

		return [
			...$this->basePath->getPath(),
			$this->segment,
		];
	}

}
