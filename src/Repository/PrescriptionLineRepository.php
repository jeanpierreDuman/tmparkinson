<?php

namespace App\Repository;

use App\Entity\Pharmacy;
use App\Entity\Prescription;
use App\Entity\PrescriptionLine;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
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

    public function getDataPills($user, $today) {
        return 
            $this->createQueryBuilder('pL')
            ->select('d.name', 'd.milligram', 'd.type', 'pL.quantity', 'pL.frequency')
            ->leftJoin('pL.prescription', 'p')
            ->leftJoin('pL.drug', 'd')
            ->leftJoin('p.user', 'u')
            ->where('p.isComplete = :isComplete')
            ->andWhere(':today BETWEEN p.dateStart AND p.dateEnd')
            ->andWhere('u = :user')
            ->setParameter('user', $user)
            ->setParameter('isComplete', true)
            ->setParameter('today', $today->format('Y-m-d'))
            ->getQuery()
            ->getResult()
        ;
    }
    
    public function getListWastePills(Pharmacy $pharmacy) {
        return $this->createQueryBuilder('pL')
            ->select('pL.id AS pLid', 'drug.id', 'drug.name', 'drug.milligram', 'drug.type', 'SUM(pL.unitPillWaste) as quantity', 'drug.quantityPackage')
            ->leftJoin('pL.drug', 'drug')
            ->leftJoin('pL.prescription', 'prescription')
            ->leftJoin('prescription.user', 'user')
            ->leftJoin('user.pharmacy', 'pharmacy')
            ->where('pharmacy = :pharmacy')
            ->andWhere('prescription.isComplete = :pIsComplete')
            ->andWhere('prescription.status = :pStatus')
            ->andWhere('pL.unitPillWaste IS NOT NULL')
            ->setParameter('pharmacy', $pharmacy)
            ->setParameter('pIsComplete', true)
            ->setParameter('pStatus', Prescription::PRESCRIPTION_COMPLETE)
            ->groupBy('pL')
            ->having('quantity > 0')
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
