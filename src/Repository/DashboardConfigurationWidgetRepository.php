<?php

namespace App\Repository;

use App\Entity\DashboardConfigurationWidget;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DashboardConfigurationWidget>
 *
 * @method DashboardConfigurationWidget|null find($id, $lockMode = null, $lockVersion = null)
 * @method DashboardConfigurationWidget|null findOneBy(array $criteria, array $orderBy = null)
 * @method DashboardConfigurationWidget[]    findAll()
 * @method DashboardConfigurationWidget[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DashboardConfigurationWidgetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DashboardConfigurationWidget::class);
    }

//    /**
//     * @return DashboardConfigurationWidget[] Returns an array of DashboardConfigurationWidget objects
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

//    public function findOneBySomeField($value): ?DashboardConfigurationWidget
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
