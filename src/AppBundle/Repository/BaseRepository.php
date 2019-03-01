<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

/**
 * @see https://www.tomasvotruba.cz/blog/2017/10/16/how-to-use-repository-with-doctrine-as-service-in-symfony/
 */
abstract class BaseRepository
{
    /**
     * @var EntityRepository
     */
    protected $repository;

    /**
     * @return string
     */
    abstract public function getResourceClassName();

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->repository = $entityManager->getRepository($this->getResourceClassName());
    }

    public function findAll()
    {
        return $this->repository->findAll();
    }
    public function find($id, $lockMode = null, $lockVersion = null)
    {
        return $this->repository->find($id, $lockMode, $lockVersion);
    }
    public function findOneBy(array $criteria, array $orderBy = null)
    {
        return $this->repository->findOneBy($criteria, $orderBy);
    }
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }
    public function count(array $criteria)
    {
        return $this->repository->count($criteria);
    }
}