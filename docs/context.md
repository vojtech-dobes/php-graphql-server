[⇐ back to index](readme.md) | [← Resolving fields](resolving-fields.md)

# Context

The reference GraphQL implementation includes `context` parameter passed to a field resolver. This value is meant to be global for the whole GraphQL request execution, allowing all resolvers to share resources like database connection, authentication status etc.

In typical PHP context, the whole run of your script is limited to single user request, which means that not just `context`, but all your instances and whole state of your PHP script gets reset for each user request. This means that unless your setup is meant to handle multiple user requests in single PHP script run, your resolvers can use direct dependencies in same manner as `context`.

If you want, you can setup your `context` value by creating an implementation of [`GraphQL\ContextFactory`](../src/GraphQL/ContextFactory.php) interface.



<br />
<br />

[→ Resolving abstract types](resolving-abstract-types.md)
