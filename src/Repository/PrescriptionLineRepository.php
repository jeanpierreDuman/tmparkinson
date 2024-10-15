<?php

namespace App\Repository;

use App\Entity\PrescriptionLine;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PrescriptionLine>
 */
class PrescriptionLineRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PrescriptionLine::class);
    }

    public function getDataPills($today) {
        return $this->createQueryBuilder('pL')
            ->select('d.name', 'd.milligram', 'd.type', 'pL.quantity', 'pL.frequency')
            ->leftJoin('pL.prescription', 'p')
            ->leftJoin('pL.drug', 'd')
            ->where('p.isComplete = :isComplete')
            ->andWhere(':today BETWEEN p.dateStart AND p.dateEnd')
            ->setParameter('isComplete', true)
            ->setParameter('today', $today)
            ->getQuery()
            ->getResult()
        ;
    }

    //    /**
    //     * @return PrescriptionLine[] Returns an array of PrescriptionLine objects
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

    //    public function findOneBySomeField($value): ?PrescriptionLine
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
