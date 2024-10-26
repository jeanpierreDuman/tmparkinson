<?php

namespace App\Repository;

use App\Entity\Pharmacy;
use App\Entity\PharmacyDrug;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PharmacyDrug>
 */
class PharmacyDrugRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PharmacyDrug::class);
    }

    public function getStocks(Pharmacy $pharmacy) {
        return $this->createQueryBuilder('s')
            ->leftJoin('s.pharmacy', 'pharmacy')
            ->where('s.quantity > 0 OR s.quantityToPrepare > 0')
            ->andWhere('pharmacy = :pharmacy')
            ->setParameter('pharmacy', $pharmacy)
            ->getQuery()
            ->getResult()
        ;
    }

    //    /**
    //     * @return PharmacyDrug[] Returns an array of PharmacyDrug objects
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

    //    public function findOneBySomeField($value): ?PharmacyDrug
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
