<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\Spec;


enum ExecutableDirectiveLocation: string
{

	case Query = 'QUERY';
	case Mutation = 'MUTATION';
	case Subscription = 'SUBSCRIPTION';
	case Field = 'FIELD';
	case FragmentDefinition = 'FRAGMENT_DEFINITION';
	case FragmentSpread = 'FRAGMENT_SPREAD';
	case InlineFragment = 'INLINE_FRAGMENT';

}
