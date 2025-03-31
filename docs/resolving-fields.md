[⇐ back to index](readme.md) | [← Setup](setup.md)

# Resolving fields

Process of resolving fields is where your schema gets populated with actual data. Every field in your schema has to be provided with an implementation of [`GraphQL\FieldResolver`](../src/GraphQL/FieldResolver.php) interface. Its method `resolveField` will be called for every object that the field is requested for.

Method `resolveField` receives 2 parameters: parent object value and metadata about the current field selection.

- The parent object value is a result of another of your field resolver, that was executed for field with Object type in your schema. Root type fields will receive null as parent object value.
- Metadata are provided as [`GraphQL\FieldSelection`](../src/GraphQL/FieldSelection.php) object. You can access the name of the field as specified in schema (same field resolver can be assigned to multiple fields) and values of provided arguments.

In case your field resolver throws an uncaught error, the field will be resolved with `null` value, the exception gets passed to globally configured error handler (you can read more about it in [Handling runtime errors](handling-runtime-errors.md)) and a generic field error is added to the response. If you want to output custom field error message, you can throw [`GraphQL\Exceptions\FailedToResolveFieldException`](../src/GraphQL/Exceptions/FailedToResolveFieldException.php). Such exception isn’t passed to the global error handler, so you have to handle potential logging etc. in the field resolver directly.

> [!NOTE]
> You may notice that `resolveField` has only 2 parameters unlike JS-inspired implementations with 4 parameters. In this library the typical args, context and info parameters are combined into the `FieldSelection` object.



<br />
<br />

[→ Context](context.md)
