<?php

declare(strict_types=1);

namespace Tiriel\MatchingBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Tiriel\MatchingBundle\Matching\MatchingHandler;

class SetStrategyNamespacePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $namespace = $container->getExtensionConfig('matching')[0]['strategy_namespace'];
        $container->getDefinition(MatchingHandler::class)
            ->addMethodCall('setStrategyNamespace', [$namespace]);
    }
}
