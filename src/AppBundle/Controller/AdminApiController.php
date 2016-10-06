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
use AppBundle\Entity\Recommendation;
use AppBundle\Entity\Contributor;
use AppBundle\Entity\BrowserExtension\RecommendationFactory;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class AdminApiController extends FOSRestController
{
    /**
     * @Route("/recommendation/{id}")
     * @ParamConverter("recommendation", class="AppBundle:Recommendation")
     * @View()
     */
    public function getRecommendationAction(Recommendation $recommendation, Request $request)
    {
        //This endpoint isn't intended to handle public recos
        if($recommendation->hasPublicVisibility()) throw $this->createNotFoundException(
            'No recommendation exists'
        );

        $user = $this->getUser();

        //SuperAdmin can see all
        if(!$user->isSuperAdmin()) {
            $recoContributor = $recommendation->getContributor();
            $userContributor = $user->getContributor();

            //Missing contributor
            if (!$recoContributor || !$userContributor) {
                throw $this->createNotFoundException(
                    'No recommendation exists'
                );
            }

            //Invalid Role
            if($userContributor->getRole() !== ContributorRole::AUTHOR_ROLE() && $userContributor->getRole() != ContributorRole::EDITOR_ROLE()){
                throw $this->createNotFoundException(
                    'No recommendation exists'
                );
            }

            //Author cannot access to other recos
            if($userContributor->getRole() === ContributorRole::AUTHOR_ROLE() && $userContributor->getId() !== $recoContributor->getId()) {
                throw $this->createNotFoundException(
                    'No recommendation exists'
                );
            }

            $recoOrganization = $recoContributor->getOrganization();
            $userOrganization = $userContributor->getOrganization();

            //Missing organization
            if (!$recoOrganization || !$userOrganization) {
                throw $this->createNotFoundException(
                    'No recommendation exists'
                );
            }

            //Editor cannot access reco outside of his own Organization
            if($userContributor->getRole() === ContributorRole::EDITOR_ROLE() && $userOrganization->getId() !== $recoOrganization->getId()) {
                throw $this->createNotFoundException(
                    'No recommendation exists'
                );
            }
        }

        return (new RecommendationFactory(
            function(Contributor $contributor) use($request) {
                return $request->getSchemeAndHttpHost().$this->get('vich_uploader.storage')->resolveUri($contributor, 'imageFile');
            }))->createFromRecommendation($recommendation);
    }

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
            return $this->get('router')->generate('app_adminapi_getrecommendation', ['id' => $id], Router::ABSOLUTE_URL);
        });

        return array_map(function($matchingContext) use ($factory){
            return $factory->createFromMatchingContext($matchingContext);
        }, $matchingContexts);
    }
}
