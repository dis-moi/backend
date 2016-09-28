<?php
/**
 * Created by PhpStorm.
 * User: alemaire
 * Date: 18/05/2016
 * Time: 16:58
 */

namespace AppBundle\Controller;

use AppBundle\DataTransferObject\BrowserExtensionMatchingContext;
use AppBundle\DataTransferObject\BrowserExtensionRecommendation;
use AppBundle\Entity\Alternative;
use AppBundle\Entity\BrowserExtension\MatchingContextFactory;
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
    public function getMatchingcontextsAction(){
        $matchingContexts = $this->getDoctrine()
            ->getRepository('AppBundle:MatchingContext')
            ->findAllWithPublicVisibility();

        if (!$matchingContexts) throw $this->createNotFoundException(
            'No matching contexts exists'
        );

        $factory = new MatchingContextFactory( function($id) {
            return $this->get('router')->generate('app_api_getrecommendation', ['id' => $id]);
        });

        return array_map(function($matchingContext) use ($factory){
            return $factory->createFromMatchingContext($matchingContext);
        }, $matchingContexts);
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
        $user = $this->getUser();
        if(!$user && !$recommendation->hasPublicVisibility()){
            throw $this->createAccessDeniedException();
        }
        $factory = $this->get('browser_extension.recommendation_factory');

        return $factory->createFromRecommendation($recommendation);
    }
}
