<?php

namespace App\Domain\Factory;

use App\Domain\Model\Category;
use App\Domain\Model\Enum\CategoryName;

class CategoryFactory
{
    public static function createAll(): array
    {
        return array_map(
            function (string $categoryName) { return Category::createFromName($categoryName); },
            CategoryName::getValues()
        );
    }
}
