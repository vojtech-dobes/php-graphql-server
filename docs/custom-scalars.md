[⇐ back to index](readme.md) | [← Handling runtime errors](handling-runtime-errors.md)

# Custom scalars

GraphQL allows defining your own scalar types (e.g. Date). It allows for higher type precision and convenience.

To be able to work with the custom scalar correctly, GraphQL server needs to told how to work with it in 3 scenarios:

- how to format the value in the response
- how to interpret the value in the input
- how to interpret the value as literal in operation or schema document

You define this behavior by registering a class that implements`ScalarImplementation` interface. Lets explain its methods by designing custom `Date` scalar.



## `serialize(mixed $value): mixed`

This method receives a value that field with the custom scalar type resolved to, and must format it for inclusion in the serializable response (e.g. JSON). Assuming we want to resolve our `Date` scalar to instance of `DateTime`, our `serialize` method can look like this:

```php
public function serialize(mixed $value): string
{
  if (!$value instanceof \DateTimeInterface) {
    throw new GraphQL\CannotSerializeScalarValueException();
  }

  return $value->format(\DateTimeInterface::ATOM);
}
```

First, we check whether our resolver returned `DateTimeInterface`. If not, `GraphQL\ScalarImplementation` is supposed to throw `GraphQL\CannotSerializeScalarValueException` in case it can't properly serialize the value. GrapHQL server will then add field error to the response.

If proper instance has been returned, we can convert the date to serializable string format, which can be part of the response.



## `parseRuntimeValue(mixed $value): mixed`

This method converts value provided through variable. It must validate it and optionally convert it more into convenient form. This is the opposite algorithm of `serialize`.

```php
public function parseRuntimeValue(mixed $value): \DateTimeImmutable
{
  $result = \DateTimeImmutable::createFromFormat(
    \DateTimeInterface::ATOM,
    $value,
  );

  if ($result === FALSE) {
    throw new GraphQL\CannotParseScalarRuntimeValueException();
  }

  return $result;
}
```

We create `DateTimeInterface` instance in from same format that our `serialize` method returned.



## `parseLiteralValue(GraphQL\Values\Value $value): mixed`

This method converts value found as a literal in the schema or in the executable document. For example we may want to place a default value in our schema for an argument that accepts our example `Date` scalar type. Let's require it in the form of valid string.

```php
public function parseLiteralValue(GraphQL\Values\Value $value): \DateTimeImmutable
{
  if (!$value instanceof GraphQL\Values\StringValue) {
    throw new GraphQL\Exceptions\CannotParseScalarLiteralValueException();
  }

  $result = \DateTimeImmutable::createFromFormat(
    \DateTimeInterface::ATOM,
    $value->value,
  );

  if ($result === FALSE) {
    throw new GraphQL\CannotParseScalarLiteralValueException();
  }

  return $result;
}
```

Now our schema or an incoming request can contain code like this:

```graphql
type Query {
  articles(since: Date = "2022-02-22T12:12:12+02:00")
}
```



<br />
<br />

[→ Static analysis support](static-analysis-support.md)
