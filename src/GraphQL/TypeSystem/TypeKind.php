<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\TypeSystem;


enum TypeKind: string
{

	case Enum = 'ENUM';
	case InputObject = 'INPUT_OBJECT';
	case Interface_ = 'INTERFACE';
	case List_ = 'LIST';
	case NonNull = 'NON_NULL';
	case Object_ = 'OBJECT';
	case Scalar = 'SCALAR';
	case Union = 'UNION';



	public function format(): string
	{
		return match ($this) {
			self::Enum => 'enum',
			self::InputObject => 'input object',
			self::Interface_ => 'interface',
			self::List_ => 'list',
			self::NonNull => 'non-nullable',
			self::Object_ => 'object',
			self::Scalar => 'scalar',
			self::Union => 'union',
		};
	}



	/**
	 * @phpstan-assert-if-true self::Enum|self::InputObject|self::List_|self::NonNull|self::Scalar $this
	 */
	public function isInputType(): bool
	{
		return match ($this) {
			self::Enum, self::InputObject, self::List_, self::NonNull, self::Scalar => true,
			default => false,
		};
	}



	public function isOutputType(): bool
	{
		return match ($this) {
			self::Enum, self::Interface_, self::Object_, self::Scalar, self::Union => true,
			default => false,
		};
	}

}
