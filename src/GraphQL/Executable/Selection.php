<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\Executable;

use JiriPudil;
use Vojtechdobes\GraphQL;


#[JiriPudil\SealedClasses\Sealed(permits: [
	Field::class,
	FragmentSpread::class,
	InlineFragment::class,
])]
interface Selection
{

	/** @var list<Directive<GraphQL\Values\Value<mixed>|Variable>> */
	public array $directives { get; }

}
