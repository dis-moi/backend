<?php

namespace AppBundle\Entity\BrowserExtension;

use AppBundle\Entity;
use AppBundle\Entity\BrowserExtension;


class MatchingContextFactory
{
    /**
     * MatchingContextFactory constructor.
     */
    public function __construct(callable $path_builder)
    {
        $this->pathBuilder = $path_builder;
    }

    public function createFromMatchingContext(Entity\MatchingContext $matchingContext) {
        return new BrowserExtension\MatchingContext(
            $this->pathBuilder->__invoke($matchingContext->getRecommendation()->getId()),
            $matchingContext->getUrlRegex()
        );
    }
}
