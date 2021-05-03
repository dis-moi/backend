<?php

declare(strict_types=1);

namespace App\Controller\Api\V3;

use App\Repository\ContributorRepository;
use App\Serializer\V3\NormalizerOptions;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\SerializerInterface;

class GetContributorAction extends BaseAction
{
    /**
     * @var ContributorRepository
     */
    protected $repository;

    public function __construct(SerializerInterface $serializer, ContributorRepository $repository)
    {
        parent::__construct($serializer);

        $this->repository = $repository;
    }

    /**
     * @Route("/contributors/{id}")
     * @Method("GET")
     */
    public function __invoke(Request $request): JsonResponse
    {
        $id = $request->get('id', null);
        $contributor = $this->repository->getOne((int) $id);

        if ( ! $contributor) {
            throw new NotFoundHttpException('Contributor not found.');
        }

        return $this->createResponse($contributor, [NormalizerOptions::INCLUDE_CONTRIBUTORS_DETAILS => false]);
    }
}
