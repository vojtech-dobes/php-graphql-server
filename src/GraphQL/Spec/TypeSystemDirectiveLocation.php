<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\Spec;


enum TypeSystemDirectiveLocation: string
{

	case Schema = 'SCHEMA';
	case Scalar = 'SCALAR';
	case Object_ = 'OBJECT';
	case FieldDefinition = 'FIELD_DEFINITION';
	case ArgumentDefinition = 'ARGUMENT_DEFINITION';
	case Interface_ = 'INTERFACE';
	case Union = 'UNION';
	case Enum = 'ENUM';
	case EnumValue = 'ENUM_VALUE';
	case InputObject = 'INPUT_OBJECT';
	case InputFieldDefinition = 'INPUT_FIELD_DEFINITION';

}
