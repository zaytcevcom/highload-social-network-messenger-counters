<?php

declare(strict_types=1);

namespace App\Counters\Entity\Counter;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use ZayMedia\Shared\Http\Exception\DomainExceptionModule;

final class CounterRepository
{
    /** @var EntityRepository<Counter> */
    private EntityRepository $repo;
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->repo = $em->getRepository(Counter::class);
        $this->em = $em;
    }

    public function getById(int $id): Counter
    {
        if (!$counter = $this->findById($id)) {
            throw new DomainExceptionModule(
                module: 'counter',
                message: 'error.counter_not_found',
                code: 1
            );
        }

        return $counter;
    }

    public function findById(int $id): ?Counter
    {
        return $this->repo->findOneBy(['id' => $id]);
    }

    public function add(Counter $counter): void
    {
        $this->em->persist($counter);
    }

    public function remove(Counter $counter): void
    {
        $this->em->remove($counter);
    }
}
