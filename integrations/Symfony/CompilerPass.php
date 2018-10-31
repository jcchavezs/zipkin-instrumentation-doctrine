<?php

namespace ZipkinDoctrine\Integrations\Symfony;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Parameter;
use Zipkin\Tracer;

final class CompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $containerBuilder)
    {
        if (!$containerBuilder->hasDefinition('doctrine.tracer.zipkin')) {
            $containerBuilder
                ->register('doctrine.tracer.zipkin')
                ->setClass(Tracer::class)
                ->setFactory([new Reference('zipkin.default_tracing'), 'getTracer']);
        }

        foreach ($containerBuilder->get('doctrine')->getConnectionNames() as $connection) {
            $containerBuilder
                ->getDefinition($connection)
                ->setMethodCalls([
                    [
                        'setTracer',
                        [
                            new Reference('doctrine.tracer.zipkin'),
                            new Parameter('doctrine.tracer.zipkin.options'),
                        ],
                    ],
                ]);
        }
    }
}
