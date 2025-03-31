<?php declare(strict_types=1);

return [
	// builtin scalars
	'String is covariant with String' => [
		'String',
		'String',
		true,
	],
	'String is not covariant with Boolean' => [
		'String',
		'Boolean',
		false,
	],
	'String is not covariant with Float' => [
		'String',
		'Float',
		false,
	],
	'String is not covariant with ID' => [
		'String',
		'ID',
		false,
	],
	'String is not covariant with Int' => [
		'String',
		'Int',
		false,
	],
	// non-null
	'Non-Null String is covariant with Non-Null String' => [
		'String!',
		'String!',
		true,
	],
	'Non-Null String is covariant with Nullable String' => [
		'String!',
		'String',
		true,
	],
	'Nullable String is not covariant with Non-Null String' => [
		'String',
		'String!',
		false,
	],
	// list
	'List of Non-Null String is covariant with List of Non-Null String' => [
		'[String!]',
		'[String!]',
		true,
	],
	'List of Non-Null String is covariant with List of Nullable String' => [
		'[String!]',
		'[String]',
		true,
	],
	'List of Nullable String is not covariant with List of Non-Null String' => [
		'[String]',
		'[String!]',
		false,
	],
	'List of Nullable String is covariant with List of Nullable String' => [
		'[String]',
		'[String]',
		true,
	],
	'List is not covariant with Non-List' => [
		'[String]',
		'String',
		false,
	],
	'Non-List is not covariant with List' => [
		'String',
		'[String]',
		false,
	],
	// custom types
	'type A is covariant with itself' => [
		'A',
		'A',
		true,
	],
	'type A is not covariant with type B' => [
		'A',
		'B',
		false,
	],
];
