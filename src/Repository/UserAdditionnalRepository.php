<?php

namespace App\Repository;

use App\Entity\UserAdditionnal;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserAdditionnal>
 *
 * @method UserAdditionnal|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserAdditionnal|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserAdditionnal[]    findAll()
 * @method UserAdditionnal[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserAdditionnalRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserAdditionnal::class);
    }

//    /**
//     * @return UserAdditionnal[] Returns an array of UserAdditionnal objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?UserAdditionnal
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
