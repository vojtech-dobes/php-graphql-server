<?php declare(strict_types=1);

return [
	// builtin scalars
	'String is covariant with String' => [
		'',
		'String',
		'String',
		true,
	],
	'String is not covariant with Boolean' => [
		'',
		'String',
		'Boolean',
		false,
	],
	'String is not covariant with Float' => [
		'',
		'String',
		'Float',
		false,
	],
	'String is not covariant with ID' => [
		'',
		'String',
		'ID',
		false,
	],
	'String is not covariant with Int' => [
		'',
		'String',
		'Int',
		false,
	],
	// non-null
	'Non-Null String is covariant with Non-Null String' => [
		'',
		'String!',
		'String!',
		true,
	],
	'Non-Null String is covariant with Nullable String' => [
		'',
		'String!',
		'String',
		true,
	],
	'Nullable String is not covariant with Non-Null String' => [
		'',
		'String',
		'String!',
		false,
	],
	// list
	'List of Non-Null String is covariant with List of Non-Null String' => [
		'',
		'[String!]',
		'[String!]',
		true,
	],
	'List of Non-Null String is covariant with List of Nullable String' => [
		'',
		'[String!]',
		'[String]',
		true,
	],
	'List of Nullable String is not covariant with List of Non-Null String' => [
		'',
		'[String]',
		'[String!]',
		false,
	],
	'List of Nullable String is covariant with List of Nullable String' => [
		'',
		'[String]',
		'[String]',
		true,
	],
	'List is not covariant with Non-List' => [
		'',
		'[String]',
		'String',
		false,
	],
	'Non-List is not covariant with List' => [
		'',
		'String',
		'[String]',
		false,
	],
	// custom types
	'Enum is covariant with itself' => [
		'enum E',
		'E',
		'E',
		true,
	],
	'Enum is not covariant with another Enum' => [
		'enum E enum F',
		'E',
		'F',
		false,
	],
	'Interface is covariant with itself' => [
		'interface I',
		'I',
		'I',
		true,
	],
	'Interface is not covariant with another Interface without explicit implementation' => [
		'interface I interface J',
		'I',
		'J',
		false,
	],
	'Interface implementing an Interface is covariant with that Interface' => [
		'interface I interface J implements I',
		'J',
		'I',
		true,
	],
	'Interface implemented by an Interface is not covariant with that Interface' => [
		'interface I interface J implements I',
		'I',
		'J',
		false,
	],
	'Object is covariant with itself' => [
		'type O',
		'O',
		'O',
		true,
	],
	'Object is not covariant with another Object' => [
		'type O type P',
		'O',
		'P',
		false,
	],
	'Object implementing an Interface is covariant with that Interface' => [
		'interface I type O implements I',
		'O',
		'I',
		true,
	],
	'Object not implementing an Interface is not covariant with that Interface' => [
		'interface I type O',
		'O',
		'I',
		false,
	],
	'Object is covariant with Union that contains it' => [
		'type O union U = O',
		'O',
		'U',
		true,
	],
	'Object is not covariant with Union that does not contain it' => [
		'type O union U',
		'O',
		'U',
		false,
	],
	'Scalar is covariant with itself' => [
		'scalar S',
		'S',
		'S',
		true,
		[
			'S' => Vojtechdobes\GraphQL\Builtin\StringScalarImplementation::class,
		],
	],
	'Scalar is not covariant with another Scalar' => [
		'scalar S scalar T',
		'S',
		'T',
		false,
		[
			'S' => Vojtechdobes\GraphQL\Builtin\StringScalarImplementation::class,
			'T' => Vojtechdobes\GraphQL\Builtin\StringScalarImplementation::class,
		],
	],
	'Union is covariant with itself' => [
		'union U',
		'U',
		'U',
		true,
	],
	'Union is not covariant with another Union' => [
		'union U union V',
		'U',
		'V',
		false,
	],
];
