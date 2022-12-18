<?php

namespace App\Repository;

use App\Entity\Carwash;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @method Carwash|null find($id, $lockMode = null, $lockVersion = null)
 * @method Carwash|null findOneBy(array $criteria, array $orderBy = null)
 * @method Carwash[]    findAll()
 * @method Carwash[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CarwashRepository
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
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
}
