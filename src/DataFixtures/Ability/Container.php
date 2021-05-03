<?php

declare(strict_types=1);

namespace App\DataFixtures\Ability;

use Symfony\Component\DependencyInjection\ContainerInterface;

trait Container
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function setContainer(ContainerInterface $container = null): void
    {
        $this->container = $container;
    }
}
