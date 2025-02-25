<?php

namespace App\Repository;

use App\Entity\Commande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Commande>
 */
class CommandeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Commande::class);
    }



    public function countCommandesEnAttente(): int
    {
        return $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.statut = :statut')
            ->setParameter('statut', 'En attente')
            ->getQuery()
            ->getSingleScalarResult();
    }
    public function countTotalCommandes(): int
    {
        return $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }


    public function getTotalVentes(): float
    {
        return (float) ($this->createQueryBuilder('c')
            ->select('SUM(c.montantTotal)')
            ->getQuery()
            ->getSingleScalarResult() ?? 0);
    }
    public function getChiffreAffaires(): float
    {
        return (float) ($this->createQueryBuilder('c')
            ->select('SUM(c.montantTotal)')
            ->getQuery()
            ->getSingleScalarResult()?? 0);
    }

    public function getMoyenneCommande(): float
    {
        return (float)( $this->createQueryBuilder('c')
            ->select('AVG(c.montantTotal)')
            ->getQuery()
            ->getSingleScalarResult()??0);
    }

    public function getCommandesRecentes(): array
    {
        return $this->createQueryBuilder('c')
            ->select('c.id, c.montantTotal, c.dateCommande')
            ->orderBy('c.dateCommande', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();
    }



    //    /**
    //     * @return Commande[] Returns an array of Commande objects
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

    //    public function findOneBySomeField($value): ?Commande
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
