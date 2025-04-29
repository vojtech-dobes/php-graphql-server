<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL;

use BackedEnum;
use LogicException;
use Vojtechdobes\GrammarProcessing;


final class SchemaParser
{

	private readonly Language\Parser $languageParser;



	public function __construct()
	{
		$this->languageParser = new Language\Parser();
	}



	/**
	 * @param array<string, class-string<BackedEnum>> $enumClasses
	 * @param array<string, ScalarImplementation<mixed, mixed>> $scalarImplementations
	 * @param list<string> $schemaExtensionStrings
	 * @throws Exceptions\CannotParseSchemaException
	 */
	public function parseSchema(
		string $schemaString,
		array $enumClasses,
		array $scalarImplementations,
		array $schemaExtensionStrings = [],
	): SchemaBuilder
	{
		$schemaBuilder = $this
			->parseBuiltinSchema()
			->extendWith($this->parseIntrospectionSchema())
			->extendWith($this->parseRawSchema($schemaString, allowReservedNames: false, rootDocument: false));

		foreach ($enumClasses as $enumName => $enumClass) {
			$schemaBuilder->addEnumClass($enumName, $enumClass);
		}

		foreach ($scalarImplementations as $scalarName => $scalarImplementation) {
			$schemaBuilder->addScalarImplementation($scalarName, $scalarImplementation);
		}

		foreach ($schemaExtensionStrings as $schemaExtensionString) {
			$schemaBuilder->extendWith(
				$this->parseRawSchema(
					$schemaExtensionString,
					allowReservedNames: false,
					rootDocument: false,
				),
			);
		}

		return $schemaBuilder;
	}



	/**
	 * @throws Exceptions\CannotParseSchemaException
	 */
	private function parseBuiltinSchema(): SchemaBuilder
	{
		$schemaBuilder = $this->parseRawSchema(
			file_get_contents(__DIR__ . '/schemas/builtin.October2021.graphqls'),
			allowReservedNames: false,
			rootDocument: true,
		);

		$schemaBuilder->addScalarImplementation('Boolean', new Builtin\BooleanScalarImplementation());
		$schemaBuilder->addScalarImplementation('Float', new Builtin\FloatScalarImplementation());
		$schemaBuilder->addScalarImplementation('ID', new Builtin\StringScalarImplementation());
		$schemaBuilder->addScalarImplementation('Int', new Builtin\IntScalarImplementation());
		$schemaBuilder->addScalarImplementation('String', new Builtin\StringScalarImplementation());

		return $schemaBuilder;
	}



	/**
	 * @throws Exceptions\CannotParseSchemaException
	 */
	private function parseIntrospectionSchema(): SchemaBuilder
	{
		return $this->parseRawSchema(
			file_get_contents(__DIR__ . '/schemas/introspection.October2021.graphqls'),
			allowReservedNames: true,
			rootDocument: false,
		);
	}



	/**
	 * @throws Exceptions\CannotParseSchemaException
	 */
	private function parseRawSchema(
		string $schemaString,
		bool $allowReservedNames,
		bool $rootDocument,
	): SchemaBuilder
	{
		$schemaBuilder = new SchemaBuilder(
			allowReservedNames: $allowReservedNames,
		);

		try {
			$definitions = match ($rootDocument) {
				true => $this->languageParser->parseTypeSystemDocument($schemaString),
				false => $this->languageParser->parseTypeSystemExtensionDocument($schemaString),
			};
		} catch (Exceptions\CannotParseDocumentException $e) {
			throw new Exceptions\CannotParseSchemaException($e->errors);
		}

		foreach ($definitions as $definition) {
			match (true) {
				$definition instanceof TypeSystem\Builder\RootOperationTypes => $schemaBuilder->setRootOperationTypes($definition),
				$definition instanceof TypeSystem\Builder\SchemaExtension => $schemaBuilder->addSchemaExtension($definition),
				$definition instanceof TypeSystem\DirectiveDefinition => $schemaBuilder->addDirectiveDefinition($definition),
				$definition instanceof TypeSystem\TypeDefinition => $schemaBuilder->addTypeDefinition($definition),
				$definition instanceof TypeSystem\TypeExtension => $schemaBuilder->addTypeExtension($definition),
			};
		}

		return $schemaBuilder;
	}

}
