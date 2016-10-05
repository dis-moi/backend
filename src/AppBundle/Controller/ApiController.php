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

        if (!$matchingContexts) {
            throw $this->createNotFoundException(
                'No matching contexts exists'
            );
        }

        $factory = new MatchingContextFactory( function($id) {
            return $this->get('router')->generate('app_api_getrecommendation', ['id' => $id], Router::ABSOLUTE_URL);
        });

        return array_map(function($matchingContext) use ($factory){
            return $factory->createFromMatchingContext($matchingContext);
        }, $matchingContexts);
    }

    /**
     * @Route("/recommendation/{id}")
     * @ParamConverter("recommendation", class="AppBundle:Recommendation")
     * @View()
     */
    public function getRecommendationAction(Recommendation $recommendation, Request $request)
    {
        if(!$recommendation->hasPublicVisibility()) throw $this->createNotFoundException(
            'No recommendation exists'
        );

        return (new RecommendationFactory(
            function(Contributor $contributor) use($request) {
                return $request->getSchemeAndHttpHost().$this->get('vich_uploader.storage')->resolveUri($contributor, 'imageFile');
        }))->createFromRecommendation($recommendation);
    }
}
