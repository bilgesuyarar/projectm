<?php

namespace App\Repository;

use App\Entity\Blog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Blog|null find($id, $lockMode = null, $lockVersion = null)
 * @method Blog|null findOneBy(array $criteria, array $orderBy = null)
 * @method Blog[]    findAll()
 * @method Blog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BlogRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Blog::class);
    }
    /**
     * @return Query
     */
    public function findAllOrderByDate()
    {
        return $this->createQueryBuilder('b')
            ->orderBy('b.id', 'DESC')
            ->getQuery();
//            ->getResult()
    }
    /**
     * @return Query
     */
    public function findAllActiveOrderByDate()
    {
        return $this->createQueryBuilder('b')
            ->orderBy('b.id', 'DESC')
            ->andWhere('b.status = :s')
            ->setParameter('s' , 'active')
            ->getQuery();
//            ->getResult()
    }


    /**
     * @param string $searchKey
     * @return Query
     */
    public function  findBySearch(string $searchKey)
    {
        $queryBuilder =$this->createQueryBuilder('b');
        $queryBuilder
            ->where('b.title LIKE :t')
            ->setParameter(':t', '%'.$searchKey.'%')
            ->andWhere('b.status = :s')
            ->setParameter('s' , 'active')
        ;
        return $queryBuilder
            ->orderBy('b.date', 'DESC')
            ->getQuery();
//            ->getResult();

    }


}
