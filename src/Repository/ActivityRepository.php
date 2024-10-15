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

    public function getActivityOfDay()
    {
        $today = new \DateTime('now');
        $todayCopy = new \DateTime('now');

        $dateBefore = $today->setTime(0,0,0);
        $dateAfter = $todayCopy->setTime(23,59,59);

        return $this->createQueryBuilder('a')
            ->select('a.name', 'COUNT(a.id) as count')
            ->groupBy('a.name')
            ->where('a.date BETWEEN :dateBefore AND :dateAfter')
            ->setParameter('dateBefore', $dateBefore)
            ->setParameter('dateAfter', $dateAfter)
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
