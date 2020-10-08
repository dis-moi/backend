<?php

namespace App\Controller;

use App\Entity\Contributor;
use App\Repository\ContributorRepository;
use Doctrine\ORM\EntityRepository;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController as BaseAdminController;
use EasyCorp\Bundle\EasyAdminBundle\Event\EasyAdminEvents;
use EasyCorp\Bundle\EasyAdminBundle\Search\QueryBuilder;
use FOS\UserBundle\Doctrine\UserManager;
use FOS\UserBundle\Model\UserManagerInterface;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\Routing\Router;
use Symfony\Component\Routing\RouterInterface;

class AdminController extends BaseAdminController
{
    /**
     * @var ContributorRepository
     */
    protected $contributorRepository;
    /**
     * @var UserManager
     */
    private $userManager;
    /**
     * @var QueryBuilder
     */
    private $queryBuilder;
    /**
     * @var Router
     */
    private $router;

    public function __construct(ContributorRepository $repository, UserManagerInterface $userManager, QueryBuilder $queryBuilder, RouterInterface $router)
    {
        $this->contributorRepository = $repository;
        $this->userManager = $userManager;
        $this->queryBuilder = $queryBuilder;
        $this->router = $router;
    }

    // Override User CRUD
    public function createNewUserEntity()
    {
        return $this->userManager->createUser();
    }

    public function prePersistUserEntity($user)
    {
        $this->userManager->updateUser($user, false);
    }

    public function preUpdateUserEntity($user)
    {
        $this->userManager->updateUser($user, false);
    }

    // Override Notice Search
    // Jalil: Override but does the same as super-class ?
    protected function createNoticeSearchQueryBuilder($entityClass, $searchQuery, array $searchableFields, $sortField = null, $sortDirection = null, $dqlFilter = null)
    {
        return $this->queryBuilder->createSearchQueryBuilder($this->entity, $searchQuery, $sortField, $sortDirection, $dqlFilter);
    }

    protected function findAll($entityClass, $page = 1, $maxPerPage = 15, $sortField = null, $sortDirection = null, $dqlFilter = null)
    {
        if (Contributor::class !== $entityClass) {
            return parent::findAll($entityClass, $page, $maxPerPage, $sortField, $sortDirection, $dqlFilter);
        }

        $contributors = $this->contributorRepository->getAll();

        $paginator = new Pagerfanta(new ArrayAdapter($contributors));
        $paginator->setMaxPerPage($maxPerPage);
        $paginator->setCurrentPage($page);

        return $paginator;
    }

    public function searchNoticeAction()
    {
        $query = trim($this->request->query->get('query'));
        // if the search query is empty, redirect to the 'list' action
        if ('' === $query) {
            $queryParameters = array_replace($this->request->query->all(), ['action' => 'list', 'query' => null]);
            $queryParameters = array_filter($queryParameters);

            return $this->redirect($this->router->generate('easyadmin', $queryParameters));
        }

        $searchableFields = $this->entity['search']['fields'];
        $paginator = $this->findBy(
            $this->entity['class'],
            $query,
            $searchableFields,
            $this->request->query->get('page', 1),
            $this->entity['list']['max_results'],
            isset($this->entity['search']['sort']['field']) ? $this->entity['search']['sort']['field'] : $this->request->query->get('sortField'),
            isset($this->entity['search']['sort']['direction']) ? $this->entity['search']['sort']['direction'] : $this->request->query->get('sortDirection'),
            $this->entity['search']['dql_filter']
        );
        $fields = $this->entity['list']['fields'];

        $this->dispatch(EasyAdminEvents::POST_SEARCH, [
            'fields' => $fields,
            'paginator' => $paginator,
        ]);

        $parameters = [
            'paginator' => $paginator,
            'fields' => $fields,
            'delete_form_template' => $this->createDeleteForm($this->entity['name'], '__id__')->createView(),
        ];

        return $this->executeDynamicMethod('render<EntityName>Template', ['search', $this->entity['templates']['list'], $parameters]);
    }

    public static function getDomainNameQueryBuilder(EntityRepository $er)
    {
        return $er->createQueryBuilder('domain_name')->orderBy('domain_name.name', 'ASC');
    }

    public static function getAllContributorsQueryBuilder(EntityRepository $er)
    {
        return $er->createQueryBuilder('contributor')->orderBy('contributor.name', 'ASC');
    }

    public static function getDomainsSetQueryBuilder(EntityRepository $er)
    {
        return $er->createQueryBuilder('domains_set')->orderBy('domains_set.name', 'ASC');
    }
}
