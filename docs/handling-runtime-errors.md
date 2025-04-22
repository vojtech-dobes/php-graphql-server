[⇐ back to index](readme.md) | [← Resolving abstract types](resolving-abstract-types.md)

# Handling runtime errors

Many kinds of errors can occur while resolving fields or abstract types: maybe database connection can’t be established, maybe there’s simply a bug. GraphQL service deals with this by catching whatever gets thrown in your code, in order to resolve the field with null value instead and add a field error to the response.

Swallowing exceptions isn’t good practice though. To give you a chance to log the error or deal with it in any way you prefer, you can provide the GraphQL service with an error handler. It must be a class implementing [`GraphQL\ErrorHandler`](../src/GraphQL/ErrorHandler.php) interface, which specifies 2 methods: one for field resolving errors and one for errors when resolving abstract types.

The library comes equipped with a ready-to-be-used implementation [`GraphQL\Psr3LogErrorHandler`](../src/GraphQL/Psr3LogErrorHandler.php) which will send any thrown exception to your `Psr\Log\LoggerInterface` compatible logger (like [Monolog](https://seldaek.github.io/monolog/)).



<br />
<br />

[→ Custom scalars](custom-scalars.md)
