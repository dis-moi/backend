<?php

namespace AppBundle\Entity\BrowserExtension;

use AppBundle\Entity\BrowserExtension;
use AppBundle\Entity\MatchingContext as MatchingContextEntity;

class MatchingContextFactory
{
    private $router;

    /**
     * MatchingContextFactory constructor.
     * @param $router
     */
    public function __construct(callable $path_builder)
    {
        $this->pathBuilder = $path_builder;
    }

    public function createFromMatchingContext(MatchingContextEntity $matchingContext) {
        return new BrowserExtension\MatchingContext(
            $this->pathBuilder->__invoke($matchingContext->getRecommendation()->getId()),
            $matchingContext->getUrlRegex()
        );
    }
}