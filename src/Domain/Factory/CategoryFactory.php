<?php

namespace Domain\Factory;

use Domain\Model\Category;
use Domain\Model\Enum\CategoryName;

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
