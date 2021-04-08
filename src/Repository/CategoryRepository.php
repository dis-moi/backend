<?php

declare(strict_types=1);

namespace App\Repository;

use App\Domain\Factory\CategoryFactory;
use App\Domain\Model\Category;
use Doctrine\Persistence\ObjectRepository;

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

    /** @phpstan-ignore-next-line */
    public function find($id): void
    {
        throw new \Exception('Unimplemented method.');
    }

    public function findAll(): array
    {
        return $this->categories;
    }

    /** @phpstan-ignore-next-line */
    public function findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null): void
    {
        throw new \Exception('Unimplemented method.');
    }

    /** @phpstan-ignore-next-line */
    public function findOneBy(array $criteria): void
    {
        throw new \Exception('Unimplemented method.');
    }

    public function getClassName(): string
    {
        return Category::class;
    }
}
