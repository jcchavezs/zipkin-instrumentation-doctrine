<?php

namespace ZipkinDoctrine\Integrations\Symfony;

use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle as KernelBundle;

class Bundle extends KernelBundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(
            new CompilerPass(),
            PassConfig::TYPE_AFTER_REMOVING
        );
    }
}
