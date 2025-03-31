<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL;


/**
 * @template TArguments of array<string, mixed> = array<string, mixed>
 * @template TContext = mixed
 */
final class FieldSelection
{

	public string $name {

		get {
			return $this->field->name;
		}

	}



	/**
	 * @param TArguments $arguments
	 * @param TContext $context
	 * @param list<Executable\Selection> $selectionSet
	 */
	public function __construct(
		public readonly array $arguments,
		public readonly mixed $context,
		private readonly Executable\Field $field,
		public readonly array $selectionSet,
	) {}

}
