[⇐ back to index](readme.md) | [← Context](context.md)

# Resolving abstract types

When resolving abstract types Interface or Union, GraphQL server needs to determine the actual Object type from the resolved object value. To let it solve this problem, you have to provide an [`GraphQL\AbstractTypeResolver`](../src/GraphQL/AbstractTypeResolver.php) for each abstract type present in the schema. This class is given the already resolved object value and must return known Object type name as string, using whatever logic appropriate.

Lets take a look at example:

```php
class ClassNameAbstractTypeResolver implements GraphQL\AbstractTypeResolver
{
  public function resolveAbstractType(mixed $objectValue): string
  {
    return $objectValue::class;
  }
}
```

This implementation expects the resolved value to be a PHP object, and it will use its class name as the Object type name. You can use this resolver [directly from the library](../src/GraphQL/ClassNameAbstractTypeResolver.php).



<br />
<br />

[→ Handling runtime errors](handling-runtime-errors.md)
