<?php

namespace App\Repository;

use App\Entity\Cours;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Cours>
 */
class CoursRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cours::class);
    }

    public function findCourseById($courseId)
    {
        return $this->find($courseId);
    }
    public function findByEnseignant(int $enseignantId): array
    {
        return $this->createQueryBuilder('c')
            ->join('c.IdMatiere', 'm') // Join the Matiere entity
            ->andWhere('m.idEnsg = :enseignantId') // Filter by enseignant ID
            ->setParameter('enseignantId', $enseignantId)
            ->orderBy('c.chapterNumber', 'ASC') // Order by chapter number
            ->getQuery()
            ->getResult();
    }
    //    /**
    //     * @return Cours[] Returns an array of Cours objects
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

    //    public function findOneBySomeField($value): ?Cours
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
