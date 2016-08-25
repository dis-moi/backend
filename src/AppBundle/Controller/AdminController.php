<?php
namespace AppBundle\Controller;

use JavierEguiluz\Bundle\EasyAdminBundle\Controller\AdminController as BaseAdminController;
use JavierEguiluz\Bundle\EasyAdminBundle\Event\EasyAdminEvents;
use AppBundle\Entity\ContributorRole;

class AdminController extends BaseAdminController
{
    public function createNewUserEntity()
    {
        return $this->get('fos_user.user_manager')->createUser();
    }

    public function prePersistUserEntity($user)
    {
        $this->get('fos_user.user_manager')->updateUser($user, false);
    }

    public function preUpdateUserEntity($user)
    {
        $this->get('fos_user.user_manager')->updateUser($user, false);
    }

    protected function createRecommendationListQueryBuilder($entityClass, $sortDirection, $sortField = null)
    {
        /**
         * @var \AppBundle\Repository\RecommendationRepository
         */
        $repo = $this->getDoctrine()
            ->getRepository('AppBundle:Recommendation');
        $user = $this->getUser();
        $queryBuilder = null;
        if ($user->isSuperAdmin()) {
            $queryBuilder = $repo->createQueryBuilder('r');
        } else {
            $contributor = $user->getContributor();
            if (!$contributor) {
                throw $this->createAccessDeniedException(
                    'Invalid user'
                );
            }

            switch ($contributor->getRole()) {
                case ContributorRole::AUTHOR_ROLE():
                    $queryBuilder = $repo->createContributorFilteredQueryBuilder($contributor);
                    break;
                case ContributorRole::EDITOR_ROLE():
                    $organization = $contributor->getOrganization();
                    if (!$organization) {
                        throw $this->createAccessDeniedException(
                            'Contributor has no organization'
                        );
                    }
                    $queryBuilder = $repo->createOrganizationFilteredQueryBuilder($organization);
                    break;
                default:
                    throw $this->createAccessDeniedException(
                        'Invalid role'
                    );
                    break;
            }
        }

        return $queryBuilder;
    }
}
