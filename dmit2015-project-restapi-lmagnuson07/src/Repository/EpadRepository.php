<?php

namespace App\Repository;

use App\Entity\Epad;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Epad>
 *
 * @method Epad|null find($id, $lockMode = null, $lockVersion = null)
 * @method Epad|null findOneBy(array $criteria, array $orderBy = null)
 * @method Epad[]    findAll()
 * @method Epad[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EpadRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Epad::class);
    }

//    /**
//     * @return Epad[] Returns an array of Epad objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Epad
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
