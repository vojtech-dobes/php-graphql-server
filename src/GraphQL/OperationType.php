<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL;


enum OperationType: string
{

	case Mutation = 'mutation';
	case Query = 'query';
	case Subscription = 'subscription';



	public function format(): string
	{
		return match ($this) {
			self::Mutation => 'Mutation',
			self::Query => 'Query',
			self::Subscription => 'Subscription',
		};
	}



	public function getExecutableDirectiveLocation(): Spec\ExecutableDirectiveLocation
	{
		return match ($this) {
			self::Mutation => Spec\ExecutableDirectiveLocation::Mutation,
			self::Query => Spec\ExecutableDirectiveLocation::Query,
			self::Subscription => Spec\ExecutableDirectiveLocation::Subscription,
		};
	}

}
