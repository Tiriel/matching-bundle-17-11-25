<?php

namespace Tiriel\MatchingBundle\Messenger\Message;

final class MatchVolunteerMessage
{
    public function __construct(
        public int $userId,
    ) {}
}
