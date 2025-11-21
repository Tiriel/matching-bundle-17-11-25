<?php

namespace Tiriel\MatchingBundle\Messenger\MessageHandler;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Tiriel\MatchingBundle\Matching\MatchingHandler;
use Tiriel\MatchingBundle\Messenger\Message\MatchVolunteerMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class MatchVolunteerMessageHandler
{
    protected ServiceEntityRepository $repository;

    public function __construct(
        private readonly MatchingHandler $matcher,
    ) {}

    public function __invoke(MatchVolunteerMessage $message): void
    {
        $user = $this->repository->find($message->userId);

        if (null === $user) {
            throw new \InvalidArgumentException('User not found');
        }

        $matches = [];
        foreach (['tag', 'skill', 'location'] as $value) {
            $matches[$value] = $this->matcher->match($user, $value);
        }

        dump($matches);
    }

    public function setRepository(ServiceEntityRepository $repository): void
    {
        $this->repository = $repository;
    }
}
