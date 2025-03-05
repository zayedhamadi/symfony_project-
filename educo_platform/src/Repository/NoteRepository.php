<?php

namespace App\Repository;

use App\Entity\Note;
use App\Entity\Eleve;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Question>
 */
class NoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Note::class);
    }

    public function findByEleve(\App\Entity\Eleve $eleve)     {
        return $this->createQueryBuilder('n')
            ->andWhere('n.eleve = :eleve')
            ->setParameter('eleve', $eleve)
            ->orderBy('n.quiz', 'ASC')  // Adjust ordering as needed
            ->getQuery()
            ->getResult();
    }
}
