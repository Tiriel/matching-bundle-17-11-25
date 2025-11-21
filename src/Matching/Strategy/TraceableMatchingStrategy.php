<?php

namespace Tiriel\MatchingBundle\Matching\Strategy;

use Psr\Log\LoggerInterface;
use Tiriel\MatchingBundle\Interface\MatchableUserInterface;
use Tiriel\MatchingBundle\Matching\Strategy\MatchingStrategyInterface;

class TraceableMatchingStrategy implements MatchingStrategyInterface
{
    public function __construct(
        protected readonly MatchingStrategyInterface $inner,
        protected readonly LoggerInterface $logger,
    ) {}

    public function match(MatchableUserInterface $user): iterable
    {
        $matches = $this->inner->match($user);

        $this->logger->info("Matched user with matchable entity", [
            'user' => $user->getSlug(),
            'strategy' => $this->inner::class,
            'matches' => $matches,
        ]);

        return $matches;
    }
}
