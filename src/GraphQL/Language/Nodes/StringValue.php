<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL\Language\Nodes;

use Generator;
use LogicException;
use Vojtechdobes\GrammarProcessing;
use Vojtechdobes\GraphQL;


final class StringValue implements GrammarProcessing\NodeInterpretation
{

	public function interpret(GrammarProcessing\Node $node): Generator
	{
		$value = yield $node->value;

		if (str_starts_with($value, '"""')) {
			$value = substr($value, 3, -3); // drop leading & trailing """

			$lines = preg_split('~\R~', $value);

			if ($lines === false) {
				throw new LogicException();
			}

			if (count($lines) > 1) {
				if (trim($lines[0]) === '') {
					$lines = array_slice($lines, 1);
				}

				if (trim($lines[count($lines) - 1]) === '') {
					$lines = array_slice($lines, 0, count($lines) - 1);
				}

				if ($lines === []) {
					$value = '';
				} else {
					$commonIndent = null;

					foreach ($lines as $line) {
						if ($line === '') {
							continue;
						}

						preg_match('~^(\s+)~', $line, $matches);

						if (isset($matches[0])) {
							$lineIndent = strlen($matches[0]);

							if ($commonIndent === null || $lineIndent < $commonIndent) {
								$commonIndent = $lineIndent;
							}
						} else {
							$commonIndent = 0;
						}

						if ($commonIndent === 0) {
							break;
						}
					}

					if ($commonIndent !== null && $commonIndent > 0) {
						$lines = array_map(
							static fn ($line) => substr($line, $commonIndent),
							$lines,
						);
					}

					$value = implode(PHP_EOL, $lines);
				}
			}
		} else {
			$value = trim($value, '"');
		}

		return new GraphQL\Values\StringValue($value);
	}

}
