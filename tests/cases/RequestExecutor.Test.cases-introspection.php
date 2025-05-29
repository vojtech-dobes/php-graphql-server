<?php declare(strict_types=1);

return [
	'introspection of empty schema' => [
		'type Query',
		[],
		'query { __schema { description directives { args { defaultValue description name type { kind name ofType { kind name } } } description isRepeatable locations name } mutationType { name } queryType { name } subscriptionType { name } types { description enumValues { deprecationReason description isDeprecated name } fields { args { defaultValue description name type { kind name ofType { kind name } } } deprecationReason description isDeprecated name type { kind name ofType { kind name } } } inputFields { defaultValue description name type { kind name ofType { kind name } } } interfaces { name } kind name ofType { kind name ofType { kind name } } possibleTypes { name } specifiedByURL } } }',
		[],
		[
			'data' => [
				'__schema' => [
					'description' => null,
					'directives' => [
						[
							'args' => [
								[
									'defaultValue' => '"No longer supported"',
									'description' => null,
									'name' => 'reason',
									'type' => ['kind' => 'SCALAR', 'name' => 'String', 'ofType' => null],
								],
							],
							'description' => null,
							'isRepeatable' => false,
							'locations' => ['FIELD_DEFINITION', 'ENUM_VALUE'],
							'name' => 'deprecated',
						],
						[
							'args' => [
								[
									'defaultValue' => null,
									'description' => null,
									'name' => 'if',
									'type' => [
										'kind' => 'NON_NULL',
										'name' => null,
										'ofType' => ['kind' => 'SCALAR', 'name' => 'Boolean'],
									],
								],
							],
							'description' => null,
							'isRepeatable' => false,
							'locations' => ['FIELD', 'FRAGMENT_SPREAD', 'INLINE_FRAGMENT'],
							'name' => 'include',
						],
						[
							'args' => [
								[
									'defaultValue' => null,
									'description' => null,
									'name' => 'if',
									'type' => [
										'kind' => 'NON_NULL',
										'name' => null,
										'ofType' => ['kind' => 'SCALAR', 'name' => 'Boolean'],
									],
								],
							],
							'description' => null,
							'isRepeatable' => false,
							'locations' => ['FIELD', 'FRAGMENT_SPREAD', 'INLINE_FRAGMENT'],
							'name' => 'skip',
						],
						[
							'args' => [
								[
									'defaultValue' => null,
									'description' => null,
									'name' => 'url',
									'type' => [
										'kind' => 'NON_NULL',
										'name' => null,
										'ofType' => ['kind' => 'SCALAR', 'name' => 'String'],
									],
								],
							],
							'description' => null,
							'isRepeatable' => false,
							'locations' => ['SCALAR'],
							'name' => 'specifiedBy',
						],
					],
					'mutationType' => null,
					'queryType' => ['name' => 'Query'],
					'subscriptionType' => null,
					'types' => [
						[
							'description' => null,
							'enumValues' => null,
							'fields' => null,
							'inputFields' => null,
							'interfaces' => null,
							'kind' => 'SCALAR',
							'name' => 'Boolean',
							'ofType' => null,
							'possibleTypes' => null,
							'specifiedByURL' => null,
						],
						[
							'description' => null,
							'enumValues' => null,
							'fields' => null,
							'inputFields' => null,
							'interfaces' => null,
							'kind' => 'SCALAR',
							'name' => 'Float',
							'ofType' => null,
							'possibleTypes' => null,
							'specifiedByURL' => null,
						],
						[
							'description' => null,
							'enumValues' => null,
							'fields' => null,
							'inputFields' => null,
							'interfaces' => null,
							'kind' => 'SCALAR',
							'name' => 'ID',
							'ofType' => null,
							'possibleTypes' => null,
							'specifiedByURL' => null,
						],
						[
							'description' => null,
							'enumValues' => null,
							'fields' => null,
							'inputFields' => null,
							'interfaces' => null,
							'kind' => 'SCALAR',
							'name' => 'Int',
							'ofType' => null,
							'possibleTypes' => null,
							'specifiedByURL' => null,
						],
						[
							'description' => null,
							'enumValues' => null,
							'fields' => null,
							'inputFields' => null,
							'interfaces' => null,
							'kind' => 'SCALAR',
							'name' => 'String',
							'ofType' => null,
							'possibleTypes' => null,
							'specifiedByURL' => null,
						],
						[
							'description' => null,
							'enumValues' => null,
							'fields' => [
								[
									'args' => [],
									'deprecationReason' => null,
									'description' => null,
									'isDeprecated' => false,
									'name' => 'description',
									'type' => ['kind' => 'SCALAR', 'name' => 'String', 'ofType' => null],
								],
								[
									'args' => [],
									'deprecationReason' => null,
									'description' => null,
									'isDeprecated' => false,
									'name' => 'types',
									'type' => [
										'kind' => 'NON_NULL',
										'name' => null,
										'ofType' => ['kind' => 'LIST', 'name' => null],
									],
								],
								[
									'args' => [],
									'deprecationReason' => null,
									'description' => null,
									'isDeprecated' => false,
									'name' => 'queryType',
									'type' => [
										'kind' => 'NON_NULL',
										'name' => null,
										'ofType' => ['kind' => 'OBJECT', 'name' => '__Type'],
									],
								],
								[
									'args' => [],
									'deprecationReason' => null,
									'description' => null,
									'isDeprecated' => false,
									'name' => 'mutationType',
									'type' => ['kind' => 'OBJECT', 'name' => '__Type', 'ofType' => null],
								],
								[
									'args' => [],
									'deprecationReason' => null,
									'description' => null,
									'isDeprecated' => false,
									'name' => 'subscriptionType',
									'type' => ['kind' => 'OBJECT', 'name' => '__Type', 'ofType' => null],
								],
								[
									'args' => [],
									'deprecationReason' => null,
									'description' => null,
									'isDeprecated' => false,
									'name' => 'directives',
									'type' => [
										'kind' => 'NON_NULL',
										'name' => null,
										'ofType' => ['kind' => 'LIST', 'name' => null],
									],
								],
							],
							'inputFields' => null,
							'interfaces' => [],
							'kind' => 'OBJECT',
							'name' => '__Schema',
							'ofType' => null,
							'possibleTypes' => null,
							'specifiedByURL' => null,
						],
						[
							'description' => null,
							'enumValues' => null,
							'fields' => [
								[
									'args' => [],
									'deprecationReason' => null,
									'description' => null,
									'isDeprecated' => false,
									'name' => 'kind',
									'type' => [
										'kind' => 'NON_NULL',
										'name' => null,
										'ofType' => ['kind' => 'ENUM', 'name' => '__TypeKind'],
									],
								],
								[
									'args' => [],
									'deprecationReason' => null,
									'description' => null,
									'isDeprecated' => false,
									'name' => 'name',
									'type' => ['kind' => 'SCALAR', 'name' => 'String', 'ofType' => null],
								],
								[
									'args' => [],
									'deprecationReason' => null,
									'description' => null,
									'isDeprecated' => false,
									'name' => 'description',
									'type' => ['kind' => 'SCALAR', 'name' => 'String', 'ofType' => null],
								],
								[
									'args' => [
										[
											'defaultValue' => 'false',
											'description' => null,
											'name' => 'includeDeprecated',
											'type' => ['kind' => 'SCALAR', 'name' => 'Boolean', 'ofType' => null],
										],
									],
									'deprecationReason' => null,
									'description' => null,
									'isDeprecated' => false,
									'name' => 'fields',
									'type' => [
										'kind' => 'LIST',
										'name' => null,
										'ofType' => ['kind' => 'NON_NULL', 'name' => null],
									],
								],
								[
									'args' => [],
									'deprecationReason' => null,
									'description' => null,
									'isDeprecated' => false,
									'name' => 'interfaces',
									'type' => [
										'kind' => 'LIST',
										'name' => null,
										'ofType' => ['kind' => 'NON_NULL', 'name' => null],
									],
								],
								[
									'args' => [],
									'deprecationReason' => null,
									'description' => null,
									'isDeprecated' => false,
									'name' => 'possibleTypes',
									'type' => [
										'kind' => 'LIST',
										'name' => null,
										'ofType' => ['kind' => 'NON_NULL', 'name' => null],
									],
								],
								[
									'args' => [
										[
											'defaultValue' => 'false',
											'description' => null,
											'name' => 'includeDeprecated',
											'type' => ['kind' => 'SCALAR', 'name' => 'Boolean', 'ofType' => null],
										],
									],
									'deprecationReason' => null,
									'description' => null,
									'isDeprecated' => false,
									'name' => 'enumValues',
									'type' => [
										'kind' => 'LIST',
										'name' => null,
										'ofType' => ['kind' => 'NON_NULL', 'name' => null],
									],
								],
								[
									'args' => [],
									'deprecationReason' => null,
									'description' => null,
									'isDeprecated' => false,
									'name' => 'inputFields',
									'type' => [
										'kind' => 'LIST',
										'name' => null,
										'ofType' => ['kind' => 'NON_NULL', 'name' => null],
									],
								],
								[
									'args' => [],
									'deprecationReason' => null,
									'description' => null,
									'isDeprecated' => false,
									'name' => 'ofType',
									'type' => ['kind' => 'OBJECT', 'name' => '__Type', 'ofType' => null],
								],
								[
									'args' => [],
									'deprecationReason' => null,
									'description' => null,
									'isDeprecated' => false,
									'name' => 'specifiedByURL',
									'type' => ['kind' => 'SCALAR', 'name' => 'String', 'ofType' => null],
								],
							],
							'inputFields' => null,
							'interfaces' => [],
							'kind' => 'OBJECT',
							'name' => '__Type',
							'ofType' => null,
							'possibleTypes' => null,
							'specifiedByURL' => null,
						],
						[
							'description' => null,
							'enumValues' => [
								[
									'deprecationReason' => null,
									'description' => null,
									'isDeprecated' => false,
									'name' => 'SCALAR',
								],
								[
									'deprecationReason' => null,
									'description' => null,
									'isDeprecated' => false,
									'name' => 'OBJECT',
								],
								[
									'deprecationReason' => null,
									'description' => null,
									'isDeprecated' => false,
									'name' => 'INTERFACE',
								],
								[
									'deprecationReason' => null,
									'description' => null,
									'isDeprecated' => false,
									'name' => 'UNION',
								],
								[
									'deprecationReason' => null,
									'description' => null,
									'isDeprecated' => false,
									'name' => 'ENUM',
								],
								[
									'deprecationReason' => null,
									'description' => null,
									'isDeprecated' => false,
									'name' => 'INPUT_OBJECT',
								],
								[
									'deprecationReason' => null,
									'description' => null,
									'isDeprecated' => false,
									'name' => 'LIST',
								],
								[
									'deprecationReason' => null,
									'description' => null,
									'isDeprecated' => false,
									'name' => 'NON_NULL',
								],
							],
							'fields' => null,
							'inputFields' => null,
							'interfaces' => null,
							'kind' => 'ENUM',
							'name' => '__TypeKind',
							'ofType' => null,
							'possibleTypes' => null,
							'specifiedByURL' => null,
						],
						[
							'description' => null,
							'enumValues' => null,
							'fields' => [
								[
									'args' => [],
									'deprecationReason' => null,
									'description' => null,
									'isDeprecated' => false,
									'name' => 'name',
									'type' => [
										'kind' => 'NON_NULL',
										'name' => null,
										'ofType' => ['kind' => 'SCALAR', 'name' => 'String'],
									],
								],
								[
									'args' => [],
									'deprecationReason' => null,
									'description' => null,
									'isDeprecated' => false,
									'name' => 'description',
									'type' => ['kind' => 'SCALAR', 'name' => 'String', 'ofType' => null],
								],
								[
									'args' => [],
									'deprecationReason' => null,
									'description' => null,
									'isDeprecated' => false,
									'name' => 'args',
									'type' => [
										'kind' => 'NON_NULL',
										'name' => null,
										'ofType' => ['kind' => 'LIST', 'name' => null],
									],
								],
								[
									'args' => [],
									'deprecationReason' => null,
									'description' => null,
									'isDeprecated' => false,
									'name' => 'type',
									'type' => [
										'kind' => 'NON_NULL',
										'name' => null,
										'ofType' => ['kind' => 'OBJECT', 'name' => '__Type'],
									],
								],
								[
									'args' => [],
									'deprecationReason' => null,
									'description' => null,
									'isDeprecated' => false,
									'name' => 'isDeprecated',
									'type' => [
										'kind' => 'NON_NULL',
										'name' => null,
										'ofType' => ['kind' => 'SCALAR', 'name' => 'Boolean'],
									],
								],
								[
									'args' => [],
									'deprecationReason' => null,
									'description' => null,
									'isDeprecated' => false,
									'name' => 'deprecationReason',
									'type' => ['kind' => 'SCALAR', 'name' => 'String', 'ofType' => null],
								],
							],
							'inputFields' => null,
							'interfaces' => [],
							'kind' => 'OBJECT',
							'name' => '__Field',
							'ofType' => null,
							'possibleTypes' => null,
							'specifiedByURL' => null,
						],
						[
							'description' => null,
							'enumValues' => null,
							'fields' => [
								[
									'args' => [],
									'deprecationReason' => null,
									'description' => null,
									'isDeprecated' => false,
									'name' => 'name',
									'type' => [
										'kind' => 'NON_NULL',
										'name' => null,
										'ofType' => ['kind' => 'SCALAR', 'name' => 'String'],
									],
								],
								[
									'args' => [],
									'deprecationReason' => null,
									'description' => null,
									'isDeprecated' => false,
									'name' => 'description',
									'type' => ['kind' => 'SCALAR', 'name' => 'String', 'ofType' => null],
								],
								[
									'args' => [],
									'deprecationReason' => null,
									'description' => null,
									'isDeprecated' => false,
									'name' => 'type',
									'type' => [
										'kind' => 'NON_NULL',
										'name' => null,
										'ofType' => ['kind' => 'OBJECT', 'name' => '__Type'],
									],
								],
								[
									'args' => [],
									'deprecationReason' => null,
									'description' => null,
									'isDeprecated' => false,
									'name' => 'defaultValue',
									'type' => ['kind' => 'SCALAR', 'name' => 'String', 'ofType' => null],
								],
							],
							'inputFields' => null,
							'interfaces' => [],
							'kind' => 'OBJECT',
							'name' => '__InputValue',
							'ofType' => null,
							'possibleTypes' => null,
							'specifiedByURL' => null,
						],
						[
							'description' => null,
							'enumValues' => null,
							'fields' => [
								[
									'args' => [],
									'deprecationReason' => null,
									'description' => null,
									'isDeprecated' => false,
									'name' => 'name',
									'type' => [
										'kind' => 'NON_NULL',
										'name' => null,
										'ofType' => ['kind' => 'SCALAR', 'name' => 'String'],
									],
								],
								[
									'args' => [],
									'deprecationReason' => null,
									'description' => null,
									'isDeprecated' => false,
									'name' => 'description',
									'type' => ['kind' => 'SCALAR', 'name' => 'String', 'ofType' => null],
								],
								[
									'args' => [],
									'deprecationReason' => null,
									'description' => null,
									'isDeprecated' => false,
									'name' => 'isDeprecated',
									'type' => [
										'kind' => 'NON_NULL',
										'name' => null,
										'ofType' => ['kind' => 'SCALAR', 'name' => 'Boolean'],
									],
								],
								[
									'args' => [],
									'deprecationReason' => null,
									'description' => null,
									'isDeprecated' => false,
									'name' => 'deprecationReason',
									'type' => ['kind' => 'SCALAR', 'name' => 'String', 'ofType' => null],
								],
							],
							'inputFields' => null,
							'interfaces' => [],
							'kind' => 'OBJECT',
							'name' => '__EnumValue',
							'ofType' => null,
							'possibleTypes' => null,
							'specifiedByURL' => null,
						],
						[
							'description' => null,
							'enumValues' => null,
							'fields' => [
								[
									'args' => [],
									'deprecationReason' => null,
									'description' => null,
									'isDeprecated' => false,
									'name' => 'name',
									'type' => [
										'kind' => 'NON_NULL',
										'name' => null,
										'ofType' => ['kind' => 'SCALAR', 'name' => 'String'],
									],
								],
								[
									'args' => [],
									'deprecationReason' => null,
									'description' => null,
									'isDeprecated' => false,
									'name' => 'description',
									'type' => ['kind' => 'SCALAR', 'name' => 'String', 'ofType' => null],
								],
								[
									'args' => [],
									'deprecationReason' => null,
									'description' => null,
									'isDeprecated' => false,
									'name' => 'locations',
									'type' => [
										'kind' => 'NON_NULL',
										'name' => null,
										'ofType' => ['kind' => 'LIST', 'name' => null],
									],
								],
								[
									'args' => [],
									'deprecationReason' => null,
									'description' => null,
									'isDeprecated' => false,
									'name' => 'args',
									'type' => [
										'kind' => 'NON_NULL',
										'name' => null,
										'ofType' => ['kind' => 'LIST', 'name' => null],
									],
								],
								[
									'args' => [],
									'deprecationReason' => null,
									'description' => null,
									'isDeprecated' => false,
									'name' => 'isRepeatable',
									'type' => [
										'kind' => 'NON_NULL',
										'name' => null,
										'ofType' => ['kind' => 'SCALAR', 'name' => 'Boolean'],
									],
								],
							],
							'inputFields' => null,
							'interfaces' => [],
							'kind' => 'OBJECT',
							'name' => '__Directive',
							'ofType' => null,
							'possibleTypes' => null,
							'specifiedByURL' => null,
						],
						[
							'description' => null,
							'enumValues' => [
								[
									'deprecationReason' => null,
									'description' => null,
									'isDeprecated' => false,
									'name' => 'QUERY',
								],
								[
									'deprecationReason' => null,
									'description' => null,
									'isDeprecated' => false,
									'name' => 'MUTATION',
								],
								[
									'deprecationReason' => null,
									'description' => null,
									'isDeprecated' => false,
									'name' => 'SUBSCRIPTION',
								],
								[
									'deprecationReason' => null,
									'description' => null,
									'isDeprecated' => false,
									'name' => 'FIELD',
								],
								[
									'deprecationReason' => null,
									'description' => null,
									'isDeprecated' => false,
									'name' => 'FRAGMENT_DEFINITION',
								],
								[
									'deprecationReason' => null,
									'description' => null,
									'isDeprecated' => false,
									'name' => 'FRAGMENT_SPREAD',
								],
								[
									'deprecationReason' => null,
									'description' => null,
									'isDeprecated' => false,
									'name' => 'INLINE_FRAGMENT',
								],
								[
									'deprecationReason' => null,
									'description' => null,
									'isDeprecated' => false,
									'name' => 'VARIABLE_DEFINITION',
								],
								[
									'deprecationReason' => null,
									'description' => null,
									'isDeprecated' => false,
									'name' => 'SCHEMA',
								],
								[
									'deprecationReason' => null,
									'description' => null,
									'isDeprecated' => false,
									'name' => 'SCALAR',
								],
								[
									'deprecationReason' => null,
									'description' => null,
									'isDeprecated' => false,
									'name' => 'OBJECT',
								],
								[
									'deprecationReason' => null,
									'description' => null,
									'isDeprecated' => false,
									'name' => 'FIELD_DEFINITION',
								],
								[
									'deprecationReason' => null,
									'description' => null,
									'isDeprecated' => false,
									'name' => 'ARGUMENT_DEFINITION',
								],
								[
									'deprecationReason' => null,
									'description' => null,
									'isDeprecated' => false,
									'name' => 'INTERFACE',
								],
								[
									'deprecationReason' => null,
									'description' => null,
									'isDeprecated' => false,
									'name' => 'UNION',
								],
								[
									'deprecationReason' => null,
									'description' => null,
									'isDeprecated' => false,
									'name' => 'ENUM',
								],
								[
									'deprecationReason' => null,
									'description' => null,
									'isDeprecated' => false,
									'name' => 'ENUM_VALUE',
								],
								[
									'deprecationReason' => null,
									'description' => null,
									'isDeprecated' => false,
									'name' => 'INPUT_OBJECT',
								],
								[
									'deprecationReason' => null,
									'description' => null,
									'isDeprecated' => false,
									'name' => 'INPUT_FIELD_DEFINITION',
								],
							],
							'fields' => null,
							'inputFields' => null,
							'interfaces' => null,
							'kind' => 'ENUM',
							'name' => '__DirectiveLocation',
							'ofType' => null,
							'possibleTypes' => null,
							'specifiedByURL' => null,
						],
						[
							'description' => null,
							'enumValues' => null,
							'fields' => [],
							'inputFields' => null,
							'interfaces' => [],
							'kind' => 'OBJECT',
							'name' => 'Query',
							'ofType' => null,
							'possibleTypes' => null,
							'specifiedByURL' => null,
						],
					],
				],
			],
		],
	],
	'introspection of enum excluding deprecated values' => [
		'enum E { M N @deprecated(reason: "N is no longer supported") } type Query',
		[],
		'query { __type(name: "E") { description enumValues { deprecationReason description isDeprecated name } fields { name } inputFields { name } interfaces { name } kind name ofType { name } possibleTypes { name } specifiedByURL } }',
		[],
		[
			'data' => [
				'__type' => [
					'description' => null,
					'enumValues' => [
						[
							'deprecationReason' => null,
							'description' => null,
							'isDeprecated' => false,
							'name' => 'M',
						],
					],
					'fields' => null,
					'inputFields' => null,
					'interfaces' => null,
					'kind' => 'ENUM',
					'name' => 'E',
					'ofType' => null,
					'possibleTypes' => null,
					'specifiedByURL' => null,
				],
			],
		],
	],
	'introspection of enum including deprecated values' => [
		'enum E { M N @deprecated(reason: "N is no longer supported") } type Query',
		[],
		'query { __type(name: "E") { description enumValues(includeDeprecated: true) { deprecationReason description isDeprecated name } fields { name } inputFields { name } interfaces { name } kind name ofType { name } possibleTypes { name } specifiedByURL } }',
		[],
		[
			'data' => [
				'__type' => [
					'description' => null,
					'enumValues' => [
						[
							'deprecationReason' => null,
							'description' => null,
							'isDeprecated' => false,
							'name' => 'M',
						],
						[
							'deprecationReason' => 'N is no longer supported',
							'description' => null,
							'isDeprecated' => true,
							'name' => 'N',
						],
					],
					'fields' => null,
					'inputFields' => null,
					'interfaces' => null,
					'kind' => 'ENUM',
					'name' => 'E',
					'ofType' => null,
					'possibleTypes' => null,
					'specifiedByURL' => null,
				],
			],
		],
	],
	'introspection of descriptions' => [
		'
			"""
			This is Schema
			"""
			schema { query: Query }

			"""
			This is Directive @d
			"""
			directive @d(
				"This is argument arg1 of directive @d"
				arg1: String

				"This is argument arg2 of directive @d"
				arg2: String
			) on ENUM

			"""
			This is Enum E
			"""
			enum E {
				"This is enum value M"
				M
				"This is enum value N"
				N
			}

			"""
			This is Input Object IO
			"""
			input IO {
				"This is input field a"
				a: String

				"This is input field b"
				b: String
			}

			"""
			This is Interface I
			"""
			interface I {
				"This is interface field a"
				a: String

				"This is interface field b with arguments"
				b(
					"This is argument arg1 of interface field b"
					arg1: String

					"This is argument arg2 of interface field b"
					arg2: String
				): String
			}

			"""
			This is Object O
			"""
			type O {
				"This is object field a"
				a: String

				"This is object field b with arguments"
				b(
					"This is argument arg1 of object field b"
					arg1: String

					"This is argument arg2 of object field b"
					arg2: String
				): String
			}

			type Query

			"""
			This is Scalar S
			"""
			scalar S

			"""
			This is Union U
			"""
			union U
		',
		[],
		'
			query {
				E: __type(name: "E") { description enumValues { description name } }
				IO: __type(name: "IO") { description inputFields { description name } }
				I: __type(name: "I") { description fields { args { description name } description name } }
				O: __type(name: "O") { description fields { args { description name } description name } }
				S: __type(name: "S") { description }
				U: __type(name: "U") { description }

				__schema {
					description
					directives {
						args {
							description
							name
						}
						name
					}
				}
			}
		',
		[],
		[
			'data' => [
				'E' => [
					'description' => 'This is Enum E',
					'enumValues' => [
						[
							'description' => 'This is enum value M',
							'name' => 'M',
						],
						[
							'description' => 'This is enum value N',
							'name' => 'N',
						],
					],
				],
				'IO' => [
					'description' => 'This is Input Object IO',
					'inputFields' => [
						[
							'description' => 'This is input field a',
							'name' => 'a',
						],
						[
							'description' => 'This is input field b',
							'name' => 'b',
						],
					],
				],
				'I' => [
					'description' => 'This is Interface I',
					'fields' => [
						[
							'args' => [],
							'description' => 'This is interface field a',
							'name' => 'a',
						],
						[
							'args' => [
								[
									'description' => 'This is argument arg1 of interface field b',
									'name' => 'arg1',
								],
								[
									'description' => 'This is argument arg2 of interface field b',
									'name' => 'arg2',
								],
							],
							'description' => 'This is interface field b with arguments',
							'name' => 'b',
						],
					],
				],
				'O' => [
					'description' => 'This is Object O',
					'fields' => [
						[
							'args' => [],
							'description' => 'This is object field a',
							'name' => 'a',
						],
						[
							'args' => [
								[
									'description' => 'This is argument arg1 of object field b',
									'name' => 'arg1',
								],
								[
									'description' => 'This is argument arg2 of object field b',
									'name' => 'arg2',
								],
							],
							'description' => 'This is object field b with arguments',
							'name' => 'b',
						],
					],
				],
				'S' => [
					'description' => 'This is Scalar S',
				],
				'U' => [
					'description' => 'This is Union U',
				],
				'__schema' => [
					'description' => 'This is Schema',
					'directives' => [
						[
							'args' => [
								[
									'description' => null,
									'name' => 'reason',
								],
							],
							'name' => 'deprecated',
						],
						[
							'args' => [
								[
									'description' => null,
									'name' => 'if',
								],
							],
							'name' => 'include',
						],
						[
							'args' => [
								[
									'description' => null,
									'name' => 'if',
								],
							],
							'name' => 'skip',
						],
						[
							'args' => [
								[
									'description' => null,
									'name' => 'url',
								],
							],
							'name' => 'specifiedBy',
						],
						[
							'args' => [
								[
									'description' => 'This is argument arg1 of directive @d',
									'name' => 'arg1',
								],
								[
									'description' => 'This is argument arg2 of directive @d',
									'name' => 'arg2',
								],
							],
							'name' => 'd',
						],
					],
				],
			],
		],
		[
			'S' => Vojtechdobes\GraphQL\Builtin\StringScalarImplementation::class,
		],
	],
	'introspection of default values' => [
		'
			enum E { M }

			input I { a: String b: Boolean }

			type O {
				a(
					argString: String = "Arg 1"
					argInt: Int = 123
					argFloat: Float = 123.456
					argBoolean: Boolean = true
					argEnum: E = M
					argNull: String = null
					argList: [String!] = ["Arg 1"]
					argObject: I = { a: "Subarg 1" b: true }
				): String
			}

			type Query
		',
		[],
		'
			query {
				__type(name: "O") { fields { args { defaultValue name } name } }
			}
		',
		[],
		[
			'data' => [
				'__type' => [
					'fields' => [
						[
							'args' => [
								[
									'defaultValue' => '"Arg 1"',
									'name' => 'argString',
								],
								[
									'defaultValue' => '123',
									'name' => 'argInt',
								],
								[
									'defaultValue' => '123.456',
									'name' => 'argFloat',
								],
								[
									'defaultValue' => 'true',
									'name' => 'argBoolean',
								],
								[
									'defaultValue' => 'M',
									'name' => 'argEnum',
								],
								[
									'defaultValue' => 'null',
									'name' => 'argNull',
								],
								[
									'defaultValue' => '["Arg 1"]',
									'name' => 'argList',
								],
								[
									'defaultValue' => '{a: "Subarg 1", b: true}',
									'name' => 'argObject',
								],
							],
							'name' => 'a',
						],
					],
				],
			],
		],
	],
	'introspection of unknown type' => [
		'type Query',
		[],
		'
			query {
				__type(name: "X") { name }
			}
		',
		[],
		[
			'data' => [
				'__type' => null,
			],
		],
	],
	'introspection of fields excluding deprecated on object type' => [
		'type O { a: String b: String @deprecated } type Query',
		[],
		'
			query {
				__type(name: "O") { fields(includeDeprecated: false) { name } name }
			}
		',
		[],
		[
			'data' => [
				'__type' => [
					'fields' => [
						[
							'name' => 'a',
						],
					],
					'name' => 'O',
				],
			],
		],
	],
	'introspection of fields including deprecated on object type' => [
		'type O { a: String b: String @deprecated } type Query',
		[],
		'
			query {
				__type(name: "O") { fields(includeDeprecated: true) { name } name }
			}
		',
		[],
		[
			'data' => [
				'__type' => [
					'fields' => [
						[
							'name' => 'a',
						],
						[
							'name' => 'b',
						],
					],
					'name' => 'O',
				],
			],
		],
	],
	'introspection of fields excluding deprecated on interface type' => [
		'interface I { a: String b: String @deprecated } type Query',
		[],
		'
			query {
				__type(name: "I") { fields(includeDeprecated: false) { name } name }
			}
		',
		[],
		[
			'data' => [
				'__type' => [
					'fields' => [
						[
							'name' => 'a',
						],
					],
					'name' => 'I',
				],
			],
		],
	],
	'introspection of fields including deprecated on interface type' => [
		'interface I { a: String b: String @deprecated } type Query',
		[],
		'
			query {
				__type(name: "I") { fields(includeDeprecated: true) { name } name }
			}
		',
		[],
		[
			'data' => [
				'__type' => [
					'fields' => [
						[
							'name' => 'a',
						],
						[
							'name' => 'b',
						],
					],
					'name' => 'I',
				],
			],
		],
	],
	'introspection of interfaces on interface type' => [
		'
			interface I implements J & K
			interface J
			interface K
			interface L
			type Query
		',
		[],
		'
			query {
				__type(name: "I") { interfaces { name } name }
			}
		',
		[],
		[
			'data' => [
				'__type' => [
					'interfaces' => [
						[
							'name' => 'J',
						],
						[
							'name' => 'K',
						],
					],
					'name' => 'I',
				],
			],
		],
	],
	'introspection of interfaces on object type' => [
		'
			type O implements I & J
			interface I
			interface J
			interface K
			type Query
		',
		[],
		'
			query {
				__type(name: "O") { interfaces { name } name }
			}
		',
		[],
		[
			'data' => [
				'__type' => [
					'interfaces' => [
						[
							'name' => 'I',
						],
						[
							'name' => 'J',
						],
					],
					'name' => 'O',
				],
			],
		],
	],
	'introspection of possibleTypes on interface type' => [
		'
			interface I
			type A implements I
			type B implements I
			type C
			type Query
		',
		[],
		'
			query {
				__type(name: "I") { name possibleTypes { name } }
			}
		',
		[],
		[
			'data' => [
				'__type' => [
					'name' => 'I',
					'possibleTypes' => [
						[
							'name' => 'A',
						],
						[
							'name' => 'B',
						],
					],
				],
			],
		],
	],
	'introspection of possibleTypes on union type' => [
		'
			union U = A | B
			type A
			type B
			type C
			type Query
		',
		[],
		'
			query {
				__type(name: "U") { name possibleTypes { name } }
			}
		',
		[],
		[
			'data' => [
				'__type' => [
					'name' => 'U',
					'possibleTypes' => [
						[
							'name' => 'A',
						],
						[
							'name' => 'B',
						],
					],
				],
			],
		],
	],
	'introspection of specifiedByURL on scalar type' => [
		'scalar S @specifiedBy(url: "U") type Query',
		[],
		'
			query {
				__type(name: "S") { name specifiedByURL }
			}
		',
		[],
		[
			'data' => [
				'__type' => [
					'name' => 'S',
					'specifiedByURL' => 'U',
				],
			],
		],
		[
			'S' => Vojtechdobes\GraphQL\Builtin\StringScalarImplementation::class,
		],
	],
	'introspection of non-named type' => [
		'type A { a: String! } type Query',
		[],
		'
			query {
				__type(name: "A") { fields { type { description enumValues { name } fields { name } inputFields { name } interfaces { name } kind name ofType { name } possibleTypes { name } specifiedByURL } } }
			}
		',
		[],
		[
			'data' => [
				'__type' => [
					'fields' => [
						[
							'type' => [
								'description' => null,
								'enumValues' => null,
								'fields' => null,
								'inputFields' => null,
								'interfaces' => null,
								'kind' => 'NON_NULL',
								'name' => null,
								'ofType' => [
									'name' => 'String',
								],
								'possibleTypes' => null,
								'specifiedByURL' => null,
							],
						],
					],
				],
			],
		],
	],
];
