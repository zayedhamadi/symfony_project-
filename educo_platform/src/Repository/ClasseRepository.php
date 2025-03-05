<?php

namespace App\Repository;

use App\Entity\Classe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Classe>
 */
class ClasseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Classe::class);
    }
    
    public function searchByName(string $searchTerm): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.nom_classe LIKE :searchTerm')
            ->setParameter('searchTerm', '%' . $searchTerm . '%')
            ->orderBy('c.nom_classe', 'ASC')
            ->getQuery()
            ->getResult();
    }

     // Méthode pour compter les classes par niveau  
    public function countClassesByNiveau($searchTerm = null)  
    {  
        $qb = $this->createQueryBuilder('c')  
            ->select('SUBSTRING(c.nom_classe, 1, 1) AS premierChar, COUNT(c) AS count')  
            ->groupBy('premierChar');  

        if ($searchTerm) {  
            $qb->andWhere('c.nom_classe LIKE :searchTerm')  
                ->setParameter('searchTerm', '%' . $searchTerm . '%');  
        }  

        // Retourner un tableau associatif avec des statistiques  
        $result = $qb->getQuery()->getResult();  

        $counts = [];  
        foreach ($result as $row) {  
            $counts[$row['premierChar']] = (int)$row['count'];  
        }  

        return $counts; // Retourne le nombre de classes par niveau  
    }  

    //    /**
    //     * @return Classe[] Returns an array of Classe objects
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

    //    public function findOneBySomeField($value): ?Classe
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
