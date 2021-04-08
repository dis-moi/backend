<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Contributor;
use App\Repository\ContributorRepository;
use App\Repository\NoticeRepository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder as DoctrineQueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController as BaseAdminController;
use EasyCorp\Bundle\EasyAdminBundle\Event\EasyAdminEvents;
use EasyCorp\Bundle\EasyAdminBundle\Search\QueryBuilder;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;

class AdminController extends BaseAdminController
{
    /**
     * @var ContributorRepository
     */
    protected $contributorRepository;
    /**
     * @var UserManagerInterface
     */
    private $userManager;
    /**
     * @var QueryBuilder
     */
    private $queryBuilder;
    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var NoticeRepository
     */
    private $noticeRepository;

    public function __construct(ContributorRepository $repository, NoticeRepository $noticeRepository, UserManagerInterface $userManager, QueryBuilder $queryBuilder, RouterInterface $router)
    {
        $this->contributorRepository = $repository;
        $this->userManager = $userManager;
        $this->queryBuilder = $queryBuilder;
        $this->router = $router;
        $this->noticeRepository = $noticeRepository;
    }

    protected function autocompleteAction(): JsonResponse
    {
        $parameterBag = $this->request->query;

        $entity = $parameterBag->get('entity');
        $query = $parameterBag->get('query');

        if ('Notice' !== $entity || !$parameterBag->get('contributor_id')) {
            return parent::autocompleteAction();
        }

        // Here we could try to find a way to filter notices by contributor_id …
        // … but right now $results = $this->get('easyadmin.autocomplete')->find()
        // does not accept any DQL :-(

        /*
         * $this->noticeRepository->createQueryForPublicNotices('n', 'c')
                    ->andWhere('c.id = :contributorId')
                    ->andWhere('n.message = :search')
                    ->andWhere('c.name = :search')
                    ->setParameter('contributorId', $parameterBag->get('contributor_id'))
                    ->setParameter('search', $query)
        */

        return parent::autocompleteAction();
    }

    // Override User CRUD
    public function createNewUserEntity(): UserInterface
    {
        return $this->userManager->createUser();
    }

    public function prePersistUserEntity(UserInterface $user): void
    {
        $this->userManager->updateUser($user);
    }

    public function preUpdateUserEntity(UserInterface $user): void
    {
        $this->userManager->updateUser($user);
    }

    /**
     * {@inheritdoc}
     *
     * @return Pagerfanta<mixed>
     */
    protected function findAll($entityClass, $page = 1, $maxPerPage = 15, $sortField = null, $sortDirection = null, $dqlFilter = null): Pagerfanta
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

    /**
     * @return mixed|RedirectResponse
     */
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
            $this->entity['search']['sort']['field'] ?? $this->request->query->get('sortField'),
            $this->entity['search']['sort']['direction'] ?? $this->request->query->get('sortDirection'),
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

    public static function getDomainNameQueryBuilder(EntityRepository $er): DoctrineQueryBuilder
    {
        return $er->createQueryBuilder('domain_name')->orderBy('domain_name.name', 'ASC');
    }

    public static function getAllContributorsQueryBuilder(EntityRepository $er): DoctrineQueryBuilder
    {
        return $er->createQueryBuilder('contributor')->orderBy('contributor.name', 'ASC');
    }

    public static function getDomainsSetQueryBuilder(EntityRepository $er): DoctrineQueryBuilder
    {
        return $er->createQueryBuilder('domains_set')->orderBy('domains_set.name', 'ASC');
    }
}
