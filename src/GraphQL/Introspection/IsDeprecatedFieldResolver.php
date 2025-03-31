<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\Introspection;

use Vojtechdobes\GraphQL;


/**
 * @implements GraphQL\FieldResolver<
 *   GraphQL\TypeSystem\EnumValueDefinition,
 *   bool,
 * >
 */
final class IsDeprecatedFieldResolver implements GraphQL\FieldResolver
{

	public function resolveField(mixed $objectValue, GraphQL\FieldSelection $field): mixed
	{
		foreach ($objectValue->directives as $directive) {
			if ($directive->name === 'deprecated') {
				return true;
			}
		}

		return false;
	}

}
