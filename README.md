# PHPStan for PSR-11

> PHPStan rules for PSR-11 `ContainerInterface`.

The `get` method in the `ContainerInterface` interface return type is `mixed`. This is not really useful for static analysis tools like PHPStan. Since it cannot infer the type of the service. You will need to add a PHPDoc comment to the variable to help PHPStan infer the type or check the type of the service at runtime with `is_a` or `instanceof` or use assertion [to narrow down the type](https://phpstan.org/writing-php-code/narrowing-types).

For example:

```php
use Bar\Service;
use Psr\Container\ContainerInterface;

class Foo
{
    public function __construct(ContainerInterface $container)
    {
        $service = $container->get(Service::class); 

        // PHPStan cannot infer the type of `$service`.
        // Check the type of the service at runtime.
        if ($service instanceof Service::class) {
        }
    }
}
```

This package adds a return type to the `get` method, based on the requested service. You do not need to add PHPDoc comments or check the type of the service at runtime.

For example:

```php
use Bar\Service;
use Psr\Container\ContainerInterface;

class Foo
{
    public function __construct(ContainerInterface $container)
    {
        $service = $container->get(Service::class);
        // PHPStan inferred the type `$service` as Bar\Service.
    }
}
```

## Installation

Install the package with Composer:

```
composer require --dev syntatis/phpstan-psr-11
```

Include `extension.neon` in your project's PHPStan config:

```
includes:
    - vendor/bnf/phpstan-psr-container/extension.neon
```

Or, use [`phpstan/extension-installer`](https://github.com/phpstan/extension-installer).

## Configuration

By default, the rule will check `Psr\Container\ContainerInterface`. If you've scoped or prefixed the interface, you can configure the rule to check the interface by providing the interface name in the configuration. For example:

```yaml
parameters:
    syntatis:
        psr-11:
            interface: 'Acme\Psr\Container\ContainerInterface'
```
