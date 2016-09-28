<?php

namespace AppBundle\Entity\BrowserExtension;

use AppBundle\Entity\MatchingContext;
use AppBundle\Entity\BrowserExtension;

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

    public function createFromMatchingContext(MatchingContext $matchingContext) {
        return new BrowserExtension\MatchingContext(
            $this->pathBuilder->__invoke($matchingContext->getId()),
            $matchingContext->getUrlRegex()
        );
    }
}