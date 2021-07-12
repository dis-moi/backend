<?php

declare(strict_types=1);

namespace App\Domain\Factory;

use App\Entity\DomainName;
use App\Entity\MatchingContext;

class MatchingContextFactory
{
    public static function create(
        DomainName $domainName,
        string $urlRegex = '.*',
        string $xpath = null
    ): MatchingContext {
        $matchingContext = new MatchingContext();
        $matchingContext->addDomainName($domainName);
        $matchingContext->setUrlRegex($urlRegex);
        if ($xpath) {
            $matchingContext->setXpath($xpath);
        }

        return $matchingContext;
    }
}
