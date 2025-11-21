<?php

namespace Tiriel\MatchingBundle;

use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use Tiriel\MatchingBundle\DependencyInjection\SetStrategyNamespacePass;
use Tiriel\MatchingBundle\DependencyInjection\SetUserRepositoryPass;
use Tiriel\MatchingBundle\DependencyInjection\TraceableMatchingStrategyPass;

class MatchingBundle extends AbstractBundle
{
    public function build(ContainerBuilder $container)
    {
        $container
            ->addCompilerPass(new SetUserRepositoryPass())
            ->addCompilerPass(new SetStrategyNamespacePass())
            ->addCompilerPass(new TraceableMatchingStrategyPass())
        ;
    }

    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import('../config/services.php');
        //$loader = new PhpFileLoader($builder, new FileLocator(__DIR__ . '/../config'));
        //$loader->load('services.php');
    }

    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->rootNode()
            ->children()
                ->stringNode('strategy_namespace')
                    ->isRequired()
                ->end()
                ->stringNode('user_repository')
                    ->isRequired()
                    ->validate()
                        ->ifFalse(fn($classname) => class_exists($classname))
                        ->thenInvalid('User repository class "%s" does not exists.')
                    ->end()
                ->end()
            ->end()
        ;
    }
}
