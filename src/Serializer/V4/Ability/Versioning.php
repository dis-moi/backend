<?php

declare(strict_types=1);

namespace App\Serializer\V4\Ability;

use App\Serializer\V3\NormalizerOptions;

trait Versioning
{
    /**
     * @param array<string, mixed> $context
     */
    public function getVersionFromContext(array $context): ?int
    {
        // we could also grab it from url prefix as a fallback
        return $context[NormalizerOptions::VERSION] ?? null;
    }

    /**
     * @param array<string, mixed> $context
     */
    public function isForV4(array $context): bool
    {
        return 4 === $this->getVersionFromContext($context);
    }
}
