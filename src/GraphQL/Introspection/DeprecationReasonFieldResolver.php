<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\Introspection;

use Vojtechdobes\GraphQL;


/**
 * @implements GraphQL\FieldResolver<
 *   GraphQL\TypeSystem\EnumValueDefinition,
 *   string|null,
 * >
 */
final class DeprecationReasonFieldResolver implements GraphQL\FieldResolver
{

	public function resolveField(mixed $objectValue, GraphQL\FieldSelection $field): mixed
	{
		foreach ($objectValue->directives as $directive) {
			if ($directive->name === 'deprecated') {
				foreach ($directive->arguments as $directiveArgument) {
					if ($directiveArgument->name === 'reason') {
						return $directiveArgument->value->getRuntimeValue();
					}
				}
			}
		}

		return null;
	}

}
