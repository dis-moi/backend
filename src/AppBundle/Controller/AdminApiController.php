<?php
namespace AppBundle\Controller;

use AppBundle\DataTransferObject\BrowserExtensionMatchingContext;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class AdminApiController extends FOSRestController
{
    /**
     * @Route("/matchingcontexts/private")
     * @View()
     */
    public function getMatchingcontextsAction()
    {
        $matchingContexts = $this->getDoctrine()
            ->getRepository('AppBundle:MatchingContext')
            ->findAllWithPrivateVisibility();

        if (!$matchingContexts) throw $this->createNotFoundException(
            'No matching contexts exists'
        );

        $factory = $this->get('browser_extension.matching_context_factory');

        return array_map(function($matchingContext) use ($factory){
            return $factory->createFromMatchingContext($matchingContext);
        }, $matchingContexts);
    }
}