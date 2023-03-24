# Zipkin instrumentation for Doctrine

[![CircleCI](https://circleci.com/gh/jcchavezs/zipkin-instrumentation-doctrine/tree/master.svg?style=svg)](https://circleci.com/gh/jcchavezs/zipkin-instrumentation-doctrine/tree/master)
[![Latest Stable Version](https://poser.pugx.org/jcchavezs/zipkin-instrumentation-doctrine/v/stable)](https://packagist.org/packages/jcchavezs/zipkin-instrumentation-doctrine)
[![Total Downloads](https://poser.pugx.org/jcchavezs/zipkin-instrumentation-doctrine/downloads)](https://packagist.org/packages/jcchavezs/zipkin-instrumentation-doctrine)
[![License](https://poser.pugx.org/jcchavezs/zipkin-instrumentation-doctrine/license)](https://packagist.org/packages/jcchavezs/zipkin-instrumentation-doctrine)

`doctrine/orm` does not expose proper interfaces for instrumentation, hence this library provides a connection extending the `Doctrine\DBAL\Connection` class and
be able to instrument all database calls.

It is important to use the specific integrations for frameworks (e.g. [Symfony](./integrations/Symfony/README.md)) in order to make this library work as it requires some configurations because plugging the
tracer is not straighforward.

## Installation

```bash
composer require jcchavezs/zipkin-instrumentation-doctrine
```

## Usage

Head to the documentation for specific frameworks:

- [Symfony](integrations/Symfony/README.md)
