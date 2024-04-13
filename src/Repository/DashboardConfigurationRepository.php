<?php

namespace App\Repository;

use App\Entity\DashboardConfiguration;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DashboardConfiguration>
 *
 * @method DashboardConfiguration|null find($id, $lockMode = null, $lockVersion = null)
 * @method DashboardConfiguration|null findOneBy(array $criteria, array $orderBy = null)
 * @method DashboardConfiguration[]    findAll()
 * @method DashboardConfiguration[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DashboardConfigurationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DashboardConfiguration::class);
    }

//    /**
//     * @return DashboardConfiguration[] Returns an array of DashboardConfiguration objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?DashboardConfiguration
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
