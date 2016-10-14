<?php
/**
 * Created by PhpStorm.
 * User: alemaire
 * Date: 18/05/2016
 * Time: 16:58
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Alternative;
use AppBundle\Entity\BrowserExtension\MatchingContextFactory;
use AppBundle\Entity\BrowserExtension\RecommendationFactory;
use AppBundle\Entity\Contributor;
use AppBundle\Entity\Recommendation;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Routing\Router;

class ApiV1Controller extends FOSRestController
{
    /**
     * @Route("/matchingcontexts")
     * @View()
     */
    public function getMatchingcontextsAction()
    {
        throw $this->createNotFoundException(
            'No matching contexts exists'
        );
    }
}
