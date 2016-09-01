<?php

namespace AppBundle\Entity\BrowserExtension;


use AppBundle\Entity\MatchingContext;
use AppBundle\Entity\BrowserExtension;
use Symfony\Component\Routing\Router;

class MatchingContextFactory
{
    private $router;

    /**
     * MatchingContextFactory constructor.
     * @param $router
     */
    public function __construct($router)
    {
        $this->router = $router;
    }

    private function getCurrentRecommendationURL($recommendation_id)
    {
        return $this->router->generate('app_api_getrecommendation', array(
            'id' => $recommendation_id
        ), Router::ABSOLUTE_URL);
    }

    public function createFromMatchingContext(MatchingContext $matchingContext) {
        return new BrowserExtension\MatchingContext(
            $this->getCurrentRecommendationURL(
                $matchingContext->getRecommendation()->getId()
            ),
            $matchingContext->getUrlRegex()
        );
    }
}