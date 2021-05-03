<?php

declare(strict_types=1);

namespace App\Serializer\V4\Ability;

use App\Serializer\V3\NormalizerOptions;

trait Versioning
{
    public function getVersionFromContext(array $context)
    {
        // we could also grab it from url prefix as a fallback
        return $context[NormalizerOptions::VERSION] ?? null;
    }

    public function isForV4(array $context)
    {
        return 4 === $this->getVersionFromContext($context);
    }
}
