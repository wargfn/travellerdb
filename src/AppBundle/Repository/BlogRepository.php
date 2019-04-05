<?php

namespace AppBundle\Repository;

class BlogRepository extends TranslatableRepository
{
    function __construct($entityManager)
    {
        parent::__construct($entityManager, $entityManager->getClassMetadata('AppBundle\Entity\Blog'));
    }

    public function findAll()
    {
        $qb = $this->createQueryBuilder('b')
            ->select('b')
            ->orderBy('b.id', 'ASC');

        return $this->getResult($qb);
    }

    public function find($id)
    {
        $qb = $this->createQueryBuilder('b')
            ->select('b')
            ->andWhere('b.id = ?1');

        $qb->setParameter(1, $id);
        return $this->getOneOrNullResult($qb);
    }

    public function topFeed()
    {
        $qb = $this->createQueryBuilder('b')
            ->select('b')
            ->orderBy('b.id', 'DSC')
            ->getMaxResults('5');
        return $this->getResult($qb);

    }
}
