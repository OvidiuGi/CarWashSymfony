<?php

namespace App\Repository;

use App\Entity\Carwash;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Carwash|null find($id, $lockMode = null, $lockVersion = null)
 * @method Carwash[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CarwashRepository extends ServiceEntityRepository
{
    private EntityManagerInterface $entityManager;

    public function __construct(ManagerRegistry $managerRegistry, EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

        parent::__construct($managerRegistry, Carwash::class);
    }

    public function add(Carwash $entity, bool $flush = true): void
    {
        $this->entityManager->persist($entity);
        if ($flush) {
            $this->entityManager->flush();
        }
    }

    public function remove(Carwash $entity, bool $flush = true): void
    {
        $this->entityManager->remove($entity);
        if ($flush) {
            $this->entityManager->flush();
        }
    }

    public function findAll(): array
    {
        $carwashes = parent::findAll();
        foreach ($carwashes as $carwash) {
            $response[] = $carwash;
        }

        return $response;
    }

    /**
     * @throws \Exception
     */
    public function findOneBy(array $criteria, array $orderBy = null): ?Carwash
    {
        $service = parent::findOneBy($criteria, $orderBy);
        if (!$service) {
            throw new \Exception('Carwash not found');
        }

        return $service;
    }
}
