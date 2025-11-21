<?php

namespace Tiriel\MatchingBundle\Matching\Strategy;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Tiriel\MatchingBundle\Interface\MatchableEntityInterface;
use Tiriel\MatchingBundle\Interface\MatchableUserInterface;

abstract class AbstractMatchingStrategy implements MatchingStrategyInterface
{
    public function __construct(
        protected readonly ServiceEntityRepository $repository,
    ) {}

    public function match(MatchableUserInterface $user): iterable
    {
        $basePrefix = substr($this->getBaseEntityName(), 0, 1);
        $matchablePrefix = substr($this->getMatchableName(), 0, 1);

        $qb = $this->repository->createQueryBuilder($basePrefix);

        return $qb
            ->innerJoin(sprintf("%s.%s", $basePrefix, $this->getMatchableName()), $matchablePrefix)
            ->where($qb->expr()->in(sprintf('%s.id', $matchablePrefix), ':ids'))
            ->setParameter('ids', $this->getMatchablesIdFromUser($user))
            ->groupBy(sprintf('%s.id', $basePrefix))
            ->orderBy($qb->expr()->count(sprintf('%s.id', $matchablePrefix)), 'DESC')
            ->getQuery()
            ->getResult();
    }

    protected function getMatchablesIdFromUser(MatchableUserInterface $user): array
    {
        $matchables = iterator_to_array($this->getMatchablesFromUser());

        return \array_map(fn(MatchableEntityInterface $e) => $e->getId(), $matchables);
    }

    abstract protected function getBaseEntityName(): string;
    abstract protected function getMatchableName(): string;
    abstract protected function getMatchablesFromUser(): iterable;
}
