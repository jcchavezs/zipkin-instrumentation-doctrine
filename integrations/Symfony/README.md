# Zipkin Doctrine instrumentation for Symfony

This extension is meant to be used with [jcchavezs/zipkin-instrumentation-symfony](https://github.com/jcchavezs/zipkin-instrumentation-symfony)

## Install

1. Include the bundle `ZipkinDoctrine\Integrations\Symfony\Bundle` in the `AppKernel.php`
2. Declare `ZipkinDoctrine\Connection` as the `wrapper_class` in the connection, as described in [DoctrineBundle documentation](https://symfony.com/doc/current/bundles/DoctrineBundle/configuration.html)
3. Voilá
