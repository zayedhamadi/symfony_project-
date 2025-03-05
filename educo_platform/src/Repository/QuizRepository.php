<?php

namespace App\Repository;

use App\Entity\Quiz;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class QuizRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Quiz::class);
    }

    /**
     * Trouve les quizzes en fonction des filtres appliqués.
     *
     * @param int|null $coursId   ID du cours pour filtrer
     * @param int|null $classeId  ID de la classe pour filtrer
     * @param string|null $search Terme de recherche pour filtrer par titre ou description
     * @return Quiz[]
     */
    public function findByFilters(?int $coursId, ?int $classeId, ?string $search): array
    {
        $qb = $this->createQueryBuilder('q');

        if ($coursId) {
            $qb->andWhere('q.cours = :coursId')
               ->setParameter('coursId', $coursId);
        }

        if ($classeId) {
            $qb->andWhere('q.classe = :classeId')
               ->setParameter('classeId', $classeId);
        }

        if ($search) {
            $qb->andWhere('q.titre LIKE :search OR q.description LIKE :search')
               ->setParameter('search', '%' . $search . '%');
        }

        return $qb->getQuery()->getResult();
    }
    public function findQuizBySearchTerm($searchValue)
{
    return $this->createQueryBuilder('q')
        ->where('q.title LIKE :searchValue')
        ->setParameter('searchValue', '%'.$searchValue.'%')
        ->getQuery()
        ->getResult();
}
}