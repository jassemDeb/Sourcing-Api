<?php

namespace App\Repository;

use App\Entity\CoreOrganizationType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CoreOrganizationType>
 *
 * @method CoreOrganizationType|null find($id, $lockMode = null, $lockVersion = null)
 * @method CoreOrganizationType|null findOneBy(array $criteria, array $orderBy = null)
 * @method CoreOrganizationType[]    findAll()
 * @method CoreOrganizationType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CoreOrganizationTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CoreOrganizationType::class);
    }

//    /**
//     * @return CoreOrganizationType[] Returns an array of CoreOrganizationType objects
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

//    public function findOneBySomeField($value): ?CoreOrganizationType
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
