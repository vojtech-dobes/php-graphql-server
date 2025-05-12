[⇐ back to index](readme.md) | [← Introduction](introduction.md)

# Setup

To execute your GraphQL schema (in order to resolve incoming requests), you can use high-level API in the form of the [`GraphQL\Service`](../src/GraphQL/Service.php) class.

```php
// $documentText = 'query GetArticle($id: String!) { article(id: $id) { name ...';
// $variables = ['id' => 'a6565d'];

$graphQLService = new GraphQL\Service( ... );

$document = $graphQLService->parseExecutableDocument($documentText);
$result = $graphQLService
  ->executeRequest(new GraphQL\Request($document, $document->operationDefinitions[0]->name, $variables))
  ->wait();
```

Setting up `Service` can be done manually or using an integration for your framework.



## With Nette Framework

The recommended way to use this library together with Nette Framework is by also installing [`vojtech-dobes/php-graphql-server-nette-integration`](https://github.com/vojtech-dobes/php-graphql-server-nette-integration?tab=readme-ov-file#nette-integration-for-php-graphql-server) package.



## Manual setup

The manual setup of everything consists of several steps:

1. Building a `GraphQL\TypeSystem\Schema` object
2. Creating `GraphQL\FieldResolverProvider` & `GraphQL\AbstractTypeResolverProvider` instances
3. Configuring [`GraphQL\ExecutableSchema`](../src/GraphQL/ExecutableSchema.php) (which represents your GraphQL schema together with resolvers, error handling etc.)
4. Creating `GraphQL\Service` object

### `GraphQL\TypeSystem\Schema` object

To build a `GraphQL\TypeSystem\Schema` object from your schema file, you can use `GraphQL\SchemaParser`.

```php
$schema = new GraphQL\SchemaParser()->parseSchema(
  schemaString: file_get_contents($schemaFilePath),
  enumClasses: [],
  scalarImplementations: [],
);
```

To avoid parsing the schema file every time, we can instead use `GraphQL\SchemaLoader`, which will store the information in generated PHP file:

```php
$schemaLoader = new GraphQL\SchemaLoader(
  autoReload: false,
  tempDir: $tempDir,
);

$schema = $schemaLoader->loadSchema(
  schemaPath: $schemaFilePath,
  enumClasses: [],
  scalarImplementations: [],
);
```

### Field & abstract type resolver providers

Each field in your schema requires a field resolver (and similarly each abstract type does require an abtract type resolver too). These are given to the execution mechanism in form of providers. In an actual production environment you will want to use something optimized, but for now let's just use simple static maps:

```php
$abstractTypeResolverProvider = new GraphQL\StaticAbstractTypeResolverProvider([]);
$fieldResolverProvider = new GraphQL\StaticFieldResolverProvider([]);
```

### `GraphQL\ExecutableSchema` object

We still have to prepare 3 more pieces of information:

- `$contextFactory` (see [Context](context.md))
- `$errorHandler` (see [Handling runtime errors](handling-runtime-errors.md))
- `$enableIntrospection` (whether we want to allow [introspection](https://graphql.org/learn/introspection/) queries by default)

Let's choose the simplest values for now. We can pick `GraphQL\NullContextFactory` for `$contextFactory` and development-friendly `GraphQL\ThrowErrorHandler` for `$errorHandler`. You might need to replace these for actual production deployment with something better!

Now we can create `GraphQL\ExecutableSchema` object.

```php
$executableSchema = new GraphQL\ExecutableSchema(
  abstractTypeResolverProvider: $abstractTypeResolverProvider,
  contextFactory: new GraphQL\NullContextFactory(),
  enableIntrospection: $enableIntrospection,
  errorHandler: new GraphQL\ThrowErrorHandler(),
  fieldResolverProvider: $fieldResolverProvider,
  schema: $schema,
);
```

### `Service` object

Finally we can create `GraphQL\Service` using a prepared helper method.

```php
$service = GraphQL\ServiceFactory::createService($executableSchema);
```



<br />
<br />

[→ Resolving fields](resolving-fields.md)
