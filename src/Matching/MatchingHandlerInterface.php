<?php

namespace Tiriel\MatchingBundle\Matching;

use Tiriel\MatchingBundle\Interface\MatchableUserInterface;

interface MatchingHandlerInterface
{
    public function match(MatchableUserInterface $user, string $strategy): iterable;
}
