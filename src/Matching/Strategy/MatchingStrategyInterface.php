<?php

namespace Tiriel\MatchingBundle\Matching\Strategy;

use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Tiriel\MatchingBundle\Interface\MatchableUserInterface;

#[Autoconfigure(tags: ['tiriel.matching_strategy'], lazy: true)]
interface MatchingStrategyInterface
{
    public function match(MatchableUserInterface $user): iterable;
}
