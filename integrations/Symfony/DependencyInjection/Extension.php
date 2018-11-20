<?php

namespace ZipkinDoctrine\Integrations\Symfony\DependencyInjection;

use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension as SymfonyExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class Extension extends SymfonyExtension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter(
            'zipkin_doctrine.options',
            $config['zipkin_doctrine']['options']
        );
    }
}
