# Zipkin instrumentation for Doctrine

`doctrine/orm` does not expose proper interfaces for instrumentation, hence this library provides a connection wrapper for decorating the `DBAL\Connection` interface and be able to instrument all database calls.

It is important to use the specific integrations for frameworks (e.g. Symfony) in order to make this library work as it requires some configurations because the wrapping is not straighforward.

## Installation

```bash
composer require jcchavezs/zipkin-instrumentation-doctrine
```

## Usage

Head to the documentation for specific framewors:

- [Symfony](integrations/Symfony/README.md)