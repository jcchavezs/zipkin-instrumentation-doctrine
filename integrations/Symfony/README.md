# Zipkin Doctrine instrumentation for Symfony

This extension is meant to be used with [jcchavezs/zipkin-instrumentation-symfony](https://github.com/jcchavezs/zipkin-instrumentation-symfony)

## Install

### Symfony 3
1. Include the bundle `ZipkinDoctrine\Integrations\Symfony\Bundle` in the `AppKernel.php` bundles' list. 
2. Declare `ZipkinDoctrine\Connection` as the `wrapper_class` in the connection, as described in [DoctrineBundle documentation](https://symfony.com/doc/current/bundles/DoctrineBundle/configuration.html)
3. Voilá

### Symfony 4
1. Go to `config/bundles.php` and and make sure both bundles are included:
```
    ZipkinBundle\ZipkinBundle::class => ['all' => true],
    ZipkinDoctrine\Integrations\Symfony\Bundle::class => ['all' => true]
```
2. Declare `ZipkinDoctrine\Connection` as the `wrapper_class` in the connection, as described in [DoctrineBundle documentation](https://symfony.com/doc/current/bundles/DoctrineBundle/configuration.html)
3. Voilá
