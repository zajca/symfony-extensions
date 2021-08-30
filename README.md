# Symfony extensions

This library contains a variety of extensions to ease work with Symfony framework 

## Install

Use composer to install the lib from packagist.

```bash
composer require zajca/symfony-extensions
```

## Usage

### Exception

Basic exception handling for http kernel.

Two Exception exists extending Exception interface: `Zajca\Extensions\Exception\ExceptionInterface`
- `Zajca\Extensions\Exception\InternalException` - Exception which will return 500 status code but will log more information than standard Exception 
- `Zajca\Extensions\Exception\PublicException` - Exception which will be serialized and shown to api consumer

### Usage in symfony

Register exception listener and use/extend predefined exceptions 
```yaml
services:
    Zajca\Extensions\Exception\ExceptionListener:
        tags:
            - { name: kernel.event_listener, event: kernel.exception }
```

### Request mapping

Argument value resolver [more in symfony docs](https://symfony.com/doc/current/controller/argument_value_resolver.html) which will map user input from request to object.

#### Examples

For examples check the tests: `Zajca\Extensions\Tests\RequestMapping\ArgumentValueResolver\Example\ControllerStub`

### Route

### Environment

