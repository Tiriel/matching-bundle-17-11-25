<?php

namespace Tiriel\MatchingBundle\Matching;

use Tiriel\MatchingBundle\Interface\MatchableUserInterface;
use Tiriel\MatchingBundle\Matching\Strategy\MatchingStrategyInterface;
use Symfony\Component\Cache\CacheItem;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class MatchingHandler implements MatchingStrategyInterface
{
    public function __construct(
        /** @var ContainerInterface<string, MatchingStrategyInterface> $strategies */
        #[AutowireLocator('tiriel.matching_strategies')]
        protected ContainerInterface $strategies,
        protected readonly string $strategyNamespace,
        protected readonly TagAwareCacheInterface $cache,
        protected readonly SluggerInterface $slugger,
    ) {}

    public function match(MatchableUserInterface $user, ?string $strategy = null): iterable
    {
        $matchingStrategy = sprintf('%s\\%sMatchingStrategy', $this->strategyNamespace, ucfirst($strategy));

        return $this->cache->get(
            $this->slugger->slug($user->getSlug()),
            function (CacheItem $item) use ($user, $matchingStrategy) {
                $result = $this->strategies->get($matchingStrategy)->match($user, $item);
                $item
                    ->set($result)
                    ->tag(['matchings'])
                    ->expiresAfter(84600);

                return $item->get();
            }
        );
    }

    public static function getName(): string
    {
        return 'matcher';
    }
}
