<?php
// src/Repository/ExamRepository.php
namespace App\Repository;

use App\Entity\Exam;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ExamRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Exam::class);
    }

    public function findByClass(?int $classId): array
    {
        $queryBuilder = $this->createQueryBuilder('e');

        if ($classId) {
            $queryBuilder
                ->andWhere('e.classe = :classId')
                ->setParameter('classId', $classId);
        }

        return $queryBuilder->getQuery()->getResult();
    }
}