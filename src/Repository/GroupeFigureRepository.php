<?php

namespace App\Repository;

use App\Entity\GroupeFigure;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method GroupeFigure|null find($id, $lockMode = null, $lockVersion = null)
 * @method GroupeFigure|null findOneBy(array $criteria, array $orderBy = null)
 * @method GroupeFigure[]    findAll()
 * @method GroupeFigure[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GroupeFigureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GroupeFigure::class);
    }

    // /**
    //  * @return GroupeFigure[] Returns an array of GroupeFigure objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?GroupeFigure
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
