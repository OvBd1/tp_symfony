<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Post>
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    public function findLatestPosts(int $limit = 10)
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.publishedAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findByCategory($categoryId)
    {
        return $this->createQueryBuilder('p')
            ->where('p.category = :categoryId')
            ->setParameter('categoryId', $categoryId)
            ->orderBy('p.publishedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
