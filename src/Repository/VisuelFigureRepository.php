<?php

namespace App\Repository;

use App\Entity\VisuelFigure;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method VisuelFigure|null find($id, $lockMode = null, $lockVersion = null)
 * @method VisuelFigure|null findOneBy(array $criteria, array $orderBy = null)
 * @method VisuelFigure[]    findAll()
 * @method VisuelFigure[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VisuelFigureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VisuelFigure::class);
    }

    // /**
    //  * @return VisuelFigure[] Returns an array of VisuelFigure objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('v.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?VisuelFigure
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
