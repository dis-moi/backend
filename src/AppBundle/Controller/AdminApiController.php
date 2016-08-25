<?php
namespace AppBundle\Controller;

use AppBundle\Entity\BrowserExtension\MatchingContextFactory;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use AppBundle\Entity\ContributorRole;
use Symfony\Component\Routing\Router;

class AdminApiController extends FOSRestController
{
    /**
     * @Route("/matchingcontexts/private")
     * @View()
     */
    public function getMatchingcontextsAction()
    {
        $repo = $this->getDoctrine()
            ->getRepository('AppBundle:MatchingContext');

        $user = $this->getUser();

        $matchingContexts = null;

        if ($user->isSuperAdmin()) {
            $matchingContexts = $repo->findAllWithPrivateVisibility();
        } else {
            $contributor = $user->getContributor();

            if (!$contributor) {
                //Invalid User
                throw $this->createNotFoundException(
                    'No matching contexts exists'
                );
            }

            switch ($contributor->getRole()) {
                case ContributorRole::AUTHOR_ROLE():
                    $matchingContexts = $repo->findAllWithContributorAndPrivateVisibility($contributor);
                    break;
                case ContributorRole::EDITOR_ROLE():
                    $organization = $contributor->getOrganization();
                    if (!$organization) {
                        //Contributor has no organization
                        throw $this->createNotFoundException(
                            'No matching contexts exists'
                        );
                    }
                    $matchingContexts = $repo->findAllWithOrganizationAndPrivateVisibility($organization);
                    break;
                default:
                    //Invalid Role
                    throw $this->createNotFoundException(
                        'No matching contexts exists'
                    );
                    break;
            }
        }


        if (!$matchingContexts) {
            //No matching contexts exists
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
}
