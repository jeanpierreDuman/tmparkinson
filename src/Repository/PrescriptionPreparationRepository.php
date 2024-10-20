<?php

namespace App\Repository;

use App\Entity\PrescriptionPreparation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PrescriptionPreparation>
 */
class PrescriptionPreparationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PrescriptionPreparation::class);
    }

    //    /**
    //     * @return PrescriptionPreparation[] Returns an array of PrescriptionPreparation objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?PrescriptionPreparation
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
