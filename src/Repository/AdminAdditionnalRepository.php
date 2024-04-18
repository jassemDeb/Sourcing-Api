<?php

namespace App\Repository;

use App\Entity\AdminAdditionnal;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AdminAdditionnal>
 *
 * @method AdminAdditionnal|null find($id, $lockMode = null, $lockVersion = null)
 * @method AdminAdditionnal|null findOneBy(array $criteria, array $orderBy = null)
 * @method AdminAdditionnal[]    findAll()
 * @method AdminAdditionnal[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdminAdditionnalRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AdminAdditionnal::class);
    }

    public function findOneByEmail($email) : AdminAdditionnal
    {
        return $this->findBy(['email' => $email]);
    }


}
