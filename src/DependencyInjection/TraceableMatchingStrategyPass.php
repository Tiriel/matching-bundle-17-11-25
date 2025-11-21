<?php

declare(strict_types=1);

namespace Tiriel\MatchingBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Tiriel\MatchingBundle\Matching\Strategy\TraceableMatchingStrategy;

class TraceableMatchingStrategyPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $container->removeDefinition(TraceableMatchingStrategy::class);

        $strategies = $container->findTaggedServiceIds('tiriel.matching_strategy');

        foreach ($strategies as $strategy => $attributes) {
            $container->register($strategy.'.traceable')
                ->setClass(TraceableMatchingStrategy::class)
                ->setDecoratedService($strategy)
                ->setAutowired(true)
                ->addTag('tiriel.matching_strategy', $attributes)
            ;
        }
    }
}
