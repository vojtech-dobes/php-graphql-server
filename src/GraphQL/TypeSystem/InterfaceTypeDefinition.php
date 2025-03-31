<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\TypeSystem;

use Vojtechdobes\GraphQL;


final class InterfaceTypeDefinition implements TypeDefinition, TypeWithFieldSelection
{

	public TypeKind $kind {

		get {
			return TypeKind::Interface_;
		}

	}

	/** @var array<string, FieldDefinition> */
	public readonly array $fieldsByName;



	/**
	 * @param list<GraphQL\Executable\Directive<GraphQL\Values\Value<mixed>>> $directives
	 * @param list<string> $implementedInterfaces
	 * @param list<FieldDefinition> $fields
	 */
	public function __construct(
		public readonly string $name,
		public readonly ?string $description,
		public readonly array $directives,
		public readonly array $implementedInterfaces,
		public readonly array $fields,
	)
	{
		$fieldsByName = [];

		foreach ($fields as $field) {
			$fieldsByName[$field->name] = $field;
		}

		$this->fieldsByName = $fieldsByName;
	}

}
