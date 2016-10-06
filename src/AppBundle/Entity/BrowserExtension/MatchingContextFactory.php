<?php

namespace AppBundle\Entity\BrowserExtension;

use AppBundle\Entity\BrowserExtension;
use AppBundle\Entity\MatchingContext as MatchingContextEntity;

class MatchingContextFactory
{

    /**
     * MatchingContextFactory constructor.
     *
     * @param callable $path_builder
     */
    public function __construct(callable $path_builder)
    {
        $this->pathBuilder = $path_builder;
    }

    /**
     * @param MatchingContextEntity $matchingContext
     *
     * @return MatchingContext
     */
    public function createFromMatchingContext(MatchingContextEntity $matchingContext)
    {
        return new BrowserExtension\MatchingContext(
            $this->pathBuilder->__invoke($matchingContext->getRecommendation()->getId()),
            $matchingContext->getUrlRegex()
        );
    }
}
