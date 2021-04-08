<?php

declare(strict_types=1);

namespace App\Domain\Factory;

use App\Domain\Model\Category;
use App\Domain\Model\Enum\CategoryName;

class CategoryFactory
{
    /**
     * @return Category[]
     */
    public static function createAll(): array
    {
        return array_map(
            function (string $categoryName) { return Category::createFromName($categoryName); },
            CategoryName::getValues()
        );
    }
}
