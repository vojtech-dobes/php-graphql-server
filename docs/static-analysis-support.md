[⇐ back to index](readme.md) | [← Custom scalars](custom-scalars.md)

# Static analysis support

If you’re using static analysis tools (like [PHPStan](https://phpstan.org/)), you can describe several generic parameters on your field resolver. The order of parameters is following: `objectValue`, `returnValue` and optionally `arguments` shape and `context` type. An example field resolver with described generic parameters (without `context`) can look like this:

```php
class AuthorEntity
{

  public function __construct(
    public readonly string $id,
  ) {}

}

/**
 * @implements FieldResolver<
 *   AuthorEntity,
 *   list<BookEntity>,
 *   array{
 *     limit: int,
 *     offset: int,
 *   },
 * >
 */
class AuthorBooksFieldResolver implements GraphQL\FieldResolver
{

  public function resolveField($parent, GraphQL\FieldSelection $field): array
  {
    return $this->booksRepository->listByAuthorId(
      authorId: $parent->id,
      limit: $field->arguments['limit'],
      offset: $field->arguments['offset'],
    );
  }

}
```
