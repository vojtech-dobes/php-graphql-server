<?php declare(strict_types=1);

namespace Vojtechdobes\GraphQL;

use BackedEnum;
use Nette;
use ReflectionClass;


final class SchemaLoader
{

	public function __construct(
		private readonly bool $autoReload,
		private readonly string $tempDir,
	) {}



	/**
	 * @param array<string, class-string<BackedEnum>> $enumClasses
	 * @param array<string, ScalarImplementation<mixed, mixed>> $scalarImplementations
	 * @param (callable(): array{
	 *   abstractTypeNamesWithResolver: list<string>,
	 *   fieldNamesWithResolver: list<string>,
	 * })|null $backendValidator
	 * @throws Exceptions\CannotGenerateCachedSchemaException
	 */
	public function loadSchema(
		string $schemaPath,
		array $enumClasses,
		array $scalarImplementations,
		?callable $backendValidator = null,
	): TypeSystem\Schema
	{
		if (is_file($schemaPath) === false) {
			throw new Exceptions\CannotGenerateCachedSchemaException(
				"Schema '{$schemaPath}' doesn't exist",
			);
		}

		$key = [
			$schemaPath,
		];

		if ($this->autoReload) {
			$key[] = filemtime($schemaPath);

			if ($backendValidator !== null) {
				$key[] = $backendValidator();
			}

			foreach ($scalarImplementations as $scalarImplementation) {
				$key[] = filemtime(new ReflectionClass($scalarImplementation::class)->getFilename());
			}
		}

		$hash = hash('xxh128', serialize($key));
		$schemaFactoryClassName = "GraphQL_{$hash}_SchemaFactory";

		if (class_exists($schemaFactoryClassName, autoload: false) === false) {
			try {
				new GeneratedClassLoader()->generateAndLoadCachedClass(
					$this->tempDir,
					$schemaFactoryClassName,
					fn ($schemaFactoryClassName) => $this->generateSchemaFactoryFile(
						$hash,
						$schemaFactoryClassName,
						$schemaPath,
						$enumClasses,
						$scalarImplementations,
						$backendValidator,
					),
				);
			} catch (Exceptions\CannotGenerateCachedClassException $e) {
				throw new Exceptions\CannotGenerateCachedSchemaException(
					$e->getMessage(),
					0,
					$e,
				);
			}
		}

		return (new $schemaFactoryClassName())->createSchema(
			customScalarImplementationRegistry: new TypeSystem\StaticRegistry($scalarImplementations),
		);
	}



	/**
	 * @param array<string, class-string<BackedEnum>> $enumClasses
	 * @param array<string, ScalarImplementation<mixed, mixed>> $scalarImplementations
	 * @param (callable(): array{
	 *   abstractTypeNamesWithResolver: list<string>,
	 *   fieldNamesWithResolver: list<string>,
	 * })|null $backendValidator
	 * @throws Exceptions\CannotGenerateCachedClassException
	 * @throws Exceptions\InvalidExecutableSchemaException
	 */
	private function generateSchemaFactoryFile(
		string $hash,
		string $schemaFactoryClassName,
		string $schemaPath,
		array $enumClasses,
		array $scalarImplementations,
		?callable $backendValidator,
	): Nette\PhpGenerator\PhpFile
	{
		$file = new Nette\PhpGenerator\PhpFile();
		$file->setStrictTypes();

		$schema = new SchemaParser()->parseSchema(
			file_get_contents($schemaPath),
			$enumClasses,
			$scalarImplementations,
		)->buildSchema();

		if ($backendValidator !== null) {
			$this->validateExecutableSchema(
				$schema,
				$backendValidator(),
			);
		}

		$directiveDefinitionRegistryClassName = "GraphQL_{$hash}_DirectiveDefinitionRegistry";
		$typeDefinitionRegistryClassName = "GraphQL_{$hash}_TypeDefinitionRegistry";

		$this->generateSchemaFactoryClass(
			$file->addClass($schemaFactoryClassName),
			$schema,
			$scalarImplementations,
			$directiveDefinitionRegistryClassName,
			$typeDefinitionRegistryClassName,
		);

		$generatedRegistryGenerator = new TypeSystem\GeneratedRegistryGenerator();

		$generatedRegistryGenerator->generateRegistry(
			$file->addClass($directiveDefinitionRegistryClassName),
			$schema->getDirectiveDefinitions(),
		);

		$generatedRegistryGenerator->generateRegistry(
			$file->addClass($typeDefinitionRegistryClassName),
			$schema->getTypeDefinitions(),
		);

		return $file;
	}



	/**
	 * @param array<string, ScalarImplementation<mixed, mixed>> $scalarImplementations
	 */
	private function generateSchemaFactoryClass(
		Nette\PhpGenerator\ClassType $class,
		TypeSystem\Schema $schema,
		array $scalarImplementations,
		string $directiveDefinitionRegistryClassName,
		string $typeDefinitionRegistryClassName,
	): void
	{
		$builtinScalarImplementations = [];

		foreach ($schema->scalarImplementationRegistry->getAll() as $scalarName => $scalarImplementation) {
			if (array_key_exists($scalarName, $scalarImplementations) === false) {
				$builtinScalarImplementations[$scalarName] = $scalarImplementation;
			}
		}

		$class->setFinal();

		$class
			->addMethod('createSchema')
			->addComment('@param ' . TypeSystem\Registry::class . '<' . ScalarImplementation::class . '> $customScalarImplementationRegistry')
			->setPublic()
			->setParameters([
				new Nette\PhpGenerator\Parameter('customScalarImplementationRegistry')
					->setType(TypeSystem\Registry::class),
			])
			->setReturnType(TypeSystem\Schema::class)
			->addBody(
				'$scalarImplementationRegistry = ?;',
				[
					Nette\PhpGenerator\Literal::new(
						TypeSystem\StaticRegistry::class,
						[
							new Nette\PhpGenerator\Literal(
								'$customScalarImplementationRegistry->getAll() + ?',
								[
									array_map(
										static fn ($builtinScalarImplementation) => new Nette\PhpGenerator\Literal(
											sprintf(
												'new %s()',
												$builtinScalarImplementation::class,
											),
										),
										$builtinScalarImplementations,
									),
								],
							),
						],
					),
				],
			)
			->addBody(
				'return ?;',
				[
					Nette\PhpGenerator\Literal::new(
						TypeSystem\Schema::class,
						[
							'description' => $schema->description,
							'directiveDefinitionRegistry' => Nette\PhpGenerator\Literal::new($directiveDefinitionRegistryClassName),
							'enumClasses' => $schema->enumClasses,
							'rootOperationTypes' => $schema->rootOperationTypes,
							'scalarImplementationRegistry' => new Nette\PhpGenerator\Literal('$scalarImplementationRegistry'),
							'typeDefinitionRegistry' => Nette\PhpGenerator\Literal::new($typeDefinitionRegistryClassName),
						],
					),
				],
			);
	}



	/**
	 * @param array{
	 *   abstractTypeNamesWithResolver: list<string>,
	 *   fieldNamesWithResolver: list<string>,
	 * } $backendValidation
	 * @throws Exceptions\InvalidExecutableSchemaException
	 */
	private function validateExecutableSchema(
		TypeSystem\Schema $schema,
		array $backendValidation,
	): void
	{
		$placeholderAbstractTypeResolver = new ClassNameAbstractTypeResolver();
		$placeholderFieldResolver = new CallbackFieldResolver(static fn () => null);

		new ExecutableSchema(
			abstractTypeResolverProvider: new StaticAbstractTypeResolverProvider(
				array_combine(
					$backendValidation['abstractTypeNamesWithResolver'],
					array_map(
						static fn () => $placeholderAbstractTypeResolver,
						$backendValidation['abstractTypeNamesWithResolver'],
					),
				),
			),
			contextFactory: new NullContextFactory(),
			enableIntrospection: false,
			errorHandler: new ThrowErrorHandler(),
			fieldResolverProvider: new StaticFieldResolverProvider(
				array_combine(
					$backendValidation['fieldNamesWithResolver'],
					array_map(
						static fn () => $placeholderFieldResolver,
						$backendValidation['fieldNamesWithResolver'],
					),
				),
			),
			schema: $schema,
		)->validate();
	}

}
