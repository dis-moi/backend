<?php

namespace AppBundle\Controller\Api;

use AppBundle\Repository\RestrictedContextRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Serializer\SerializerInterface;

class GetRestrictedContextsAction extends BaseAction
{
    protected $repository;

    public function __construct(SerializerInterface $serializer, RestrictedContextRepository $repository)
    {
        parent::__construct($serializer);

        $this->repository = $repository;
    }

    /**
     * @Route("/restrictedcontexts")
     * @Route("/restricted-contexts")
     * @Method("GET")
     */
    public function __invoke()
    {
        $restrictedContexts = $this->repository->findAll();

        return $this->createResponse($restrictedContexts);
    }
}
