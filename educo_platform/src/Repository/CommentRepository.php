<?php


// src/Repository/CommentRepository.php

namespace App\Repository;

use App\Entity\Comment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    // Method to find comments by course
    public function findCommentsByCourse($courseId)
    {
        return $this->createQueryBuilder('c')
            ->where('c.course = :courseId')
            ->setParameter('courseId', $courseId)
            ->getQuery()
            ->getResult();
    }
}
