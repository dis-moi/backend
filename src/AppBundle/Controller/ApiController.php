<?php
/**
 * Created by PhpStorm.
 * User: alemaire
 * Date: 18/05/2016
 * Time: 16:58
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Alternative;
use AppBundle\Entity\Recommendation;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class ApiController extends FOSRestController
{

    /**
     * @Route("/matchingcontexts")
     * @View()
     */
    public function getMatchingcontextsAction()
    {
        $matchingContexts = $this->getDoctrine()
            ->getRepository('AppBundle:MatchingContext')
            ->findAllWithPublicVisibility();

        if (!$matchingContexts) throw $this->createNotFoundException(
            'No matching contexts exists'
        );

        return $matchingContexts;
    }
    
    /**
     * @Route("/alternative/{id}")
     * @ParamConverter("alternative", class="AppBundle:Alternative")
     * @View()
     */
    public function getAlternativeAction(Alternative $alternative)
    {
        return $alternative;
    }

    /**
     * @Route("/recommendation/{id}")
     * @ParamConverter("recommendation", class="AppBundle:Recommendation")
     * @View()
     */
    public function getRecommendationAction(Recommendation $recommendation)
    {
        return $recommendation;
    }
}
