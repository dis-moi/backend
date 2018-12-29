<?php

namespace AppBundle\Controller\Api;

use AppBundle\Repository\ContributorRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\SerializerInterface;

class GetContributorsAction extends BaseAction
{
    protected $repository;

    public function __construct(SerializerInterface $serializer, ContributorRepository $repository)
    {
        parent::__construct($serializer);
        $this->repository = $repository;
    }

    /**
     * @Route("/contributors")
     */
    public function __invoke()
    {
        $contributors = $this->repository->getAllEnabled();

        if (!$contributors) {
            throw new NotFoundHttpException('No contributors exist');
        }

        return $this->createResponse($contributors);
    }
}
