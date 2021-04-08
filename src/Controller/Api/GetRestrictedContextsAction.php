<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Repository\RestrictedContextRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;

class GetRestrictedContextsAction extends BaseAction
{
    /**
     * @var RestrictedContextRepository
     */
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
    public function __invoke(): JsonResponse
    {
        $restrictedContexts = $this->repository->findAll();

        return $this->createResponse($restrictedContexts);
    }
}
