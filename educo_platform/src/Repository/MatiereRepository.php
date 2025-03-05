<?php

namespace App\Repository;

use App\Entity\Matiere;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Matiere>
 */
class MatiereRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Matiere::class);
    }

    public function findAllWithEnseignant(): array
    {
        return $this->createQueryBuilder('m')
            ->leftJoin('m.idEnsg', 'e')
            ->addSelect('e') // Ensures the enseignant is loaded
            ->getQuery()
            ->getResult();
    }

    public function findBySearch(string $query)
    {
        return $this->createQueryBuilder('m')
            ->leftJoin('m.idEnsg', 'e')
            ->where('m.nom LIKE :query')
            ->orWhere('e.nom LIKE :query')
            ->setParameter('query', '%' . $query . '%')
            ->orderBy('m.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }
    // src/Repository/MatiereRepository.php
    public function findAllEnseignants(): array
    {
        return $this->createQueryBuilder('m')
            ->leftJoin('m.idEnsg', 'e') // Join the Enseignant entity
            ->select('e.id, e.nom') // Select specific fields from Enseignant
            ->distinct() // Ensure unique results
            ->getQuery()
            ->getResult();
    }


    //    /**
    //     * @return Matiere[] Returns an array of Matiere objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('m.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Matiere
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
