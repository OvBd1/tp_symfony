<?php

namespace App\Repository;

use App\Entity\Comment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Comment>
 */
class CommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    public function findApprovedCommentsByPost($postId)
    {
        return $this->createQueryBuilder('c')
            ->where('c.post = :postId')
            ->andWhere('c.status = :status')
            ->setParameter('postId', $postId)
            ->setParameter('status', 'approved')
            ->orderBy('c.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findPendingComments()
    {
        return $this->createQueryBuilder('c')
            ->where('c.status = :status')
            ->setParameter('status', 'pending')
            ->orderBy('c.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
