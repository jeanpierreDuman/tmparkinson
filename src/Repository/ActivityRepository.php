<?php

namespace App\Repository;

use App\Entity\Activity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Activity>
 */
class ActivityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Activity::class);
    }

    public function getAllByMonthAndYear($year, $month) {
        return $this->createQueryBuilder('a')
            ->select("COUNT(a.id) as count", "a.name as name", "DATE_FORMAT(a.date, '%d/%m/%Y') as date")
            ->where("DATE_FORMAT(a.date, '%m') = :month")
            ->andWhere("DATE_FORMAT(a.date, '%Y') = :year")
            ->setParameter("month", $month)
            ->setParameter("year", $year)
            ->groupBy('a.name', 'date')
            ->getQuery()
            ->getResult()
        ;
    }

    public function getActivityOfDay()
    {
        $today = new \DateTime('now', new \DateTimeZone('Europe/Paris'));
        $todayCopy = new \DateTime('now', new \DateTimeZone('Europe/Paris'));

        return $this->createQueryBuilder('a')
            ->select('a.name', 'COUNT(a.id) as count')
            ->groupBy('a.name')
            ->where('a.date BETWEEN :dateBefore AND :dateAfter')
            ->setParameter('dateBefore', $today->format('Y-m-d'))
            ->setParameter('dateAfter', $todayCopy->format('Y-m-d'))
            ->getQuery()
            ->getResult()
        ;
    }

    //    /**
    //     * @return Activity[] Returns an array of Activity objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Activity
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
