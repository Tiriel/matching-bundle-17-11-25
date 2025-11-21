<?php

declare(strict_types=1);

namespace Tiriel\MatchingBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Tiriel\MatchingBundle\Messenger\MessageHandler\MatchVolunteerMessageHandler;

class SetUserRepositoryPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $repository = $container->getExtensionConfig('matching')[0]['user_repository'];
        $container->getDefinition(MatchVolunteerMessageHandler::class)
            ->addMethodCall('setRepository', [$container->getDefinition($repository)]);
    }
}
