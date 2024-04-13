<?php

namespace App\Repository;

use App\Entity\CoreOrganization;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CoreOrganization>
 *
 * @method CoreOrganization|null find($id, $lockMode = null, $lockVersion = null)
 * @method CoreOrganization|null findOneBy(array $criteria, array $orderBy = null)
 * @method CoreOrganization[]    findAll()
 * @method CoreOrganization[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CoreOrganizationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CoreOrganization::class);
    }

//    /**
//     * @return CoreOrganization[] Returns an array of CoreOrganization objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?CoreOrganization
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
