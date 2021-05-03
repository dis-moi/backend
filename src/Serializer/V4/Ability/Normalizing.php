<?php

declare(strict_types=1);

namespace App\Serializer\V4\Ability;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

trait Normalizing
{
    /**
     * @var NormalizerInterface
     */
    protected $normalizer;

    /**
     * Sets the owning Normalizer object.
     */
    public function setNormalizer(NormalizerInterface $normalizer): void
    {
        $this->normalizer = $normalizer;
    }
}
