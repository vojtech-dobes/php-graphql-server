<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL;

use LogicException;
use Vojtechdobes\GrammarProcessing;


final class ExecutableDocumentParser
{

	private Language\Parser $languageParser;



	public function __construct()
	{
		$this->languageParser = new Language\Parser();
	}



	/**
	 * @throws Exceptions\CannotParseExecutableDocumentException
	 */
	public function parseExecutableDocument(
		TypeSystem\Schema $schema,
		string $documentString,
	): ExecutableDocumentBuilder
	{
		$executableDocumentBuilder = new ExecutableDocumentBuilder($schema);

		try {
			$definitions = $this->languageParser->parseExecutableDocument($documentString);
		} catch (Exceptions\CannotParseDocumentException $e) {
			throw new Exceptions\CannotParseExecutableDocumentException($e->errors);
		}

		foreach ($definitions as $definition) {
			match (TRUE) {
				$definition instanceof Executable\FragmentDefinition => $executableDocumentBuilder->addFragmentDefinition($definition),
				$definition instanceof Executable\OperationDefinition => $executableDocumentBuilder->addOperationDefinition($definition),
			};
		}

		return $executableDocumentBuilder;
	}

}
