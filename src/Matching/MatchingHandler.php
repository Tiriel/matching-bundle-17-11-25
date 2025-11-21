<?php

namespace Tiriel\MatchingBundle\Matching;

use Symfony\Component\DependencyInjection\ServiceLocator;
use Tiriel\MatchingBundle\Interface\MatchableUserInterface;
use Tiriel\MatchingBundle\Matching\Strategy\MatchingStrategyInterface;
use Symfony\Component\Cache\CacheItem;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class MatchingHandler implements MatchingHandlerInterface
{
    protected string $strategyNamespace;

    public function __construct(
        /** @var ServiceLocator<string, MatchingStrategyInterface> $strategies */
        #[AutowireLocator('tiriel.matching_strategy')]
        protected ServiceLocator $strategies,
        protected readonly TagAwareCacheInterface $cache,
        protected readonly SluggerInterface $slugger,
    ) {
        dump($strategies->getProvidedServices());
    }

    public function setStrategyNamespace(string $strategyNamespace): void
    {
        $this->strategyNamespace = $strategyNamespace;
    }

    public function match(MatchableUserInterface $user, string $strategy): iterable
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
