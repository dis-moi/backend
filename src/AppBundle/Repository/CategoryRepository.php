<?php

namespace AppBundle\Repository;

use Doctrine\Persistence\ObjectRepository;
use Domain\Factory\CategoryFactory;
use Domain\Model\Category;

class CategoryRepository implements ObjectRepository
{
    /**
     * @var Category[]
     */
    private $categories;

    public function __construct()
    {
        $this->categories = CategoryFactory::createAll();
    }

    public function find($id)
    {
        throw new \Exception('Unimplemented method.');
    }

    public function findAll()
    {
        return $this->categories;
    }

    public function findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null)
    {
        // TODO: Implement findBy() method.
        throw new \Exception('Unimplemented method.');
    }

    public function findOneBy(array $criteria)
    {
        // TODO: Implement findOneBy() method.
        throw new \Exception('Unimplemented method.');
    }

    /**
     * @return string
     */
    public function getClassName()
    {
        return Category::class;
    }
}
