<?php

declare(strict_types=1);

namespace App\Controller\Api\V3;

use App\Repository\MatchingContextRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\SerializerInterface;

class GetMatchingContextsAction extends BaseAction
{
    /**
     * @var MatchingContextRepository
     */
    protected $repository;

    public function __construct(SerializerInterface $serializer, MatchingContextRepository $repository)
    {
        parent::__construct($serializer);

        $this->repository = $repository;
    }

    /**
     * @Route("/matchingcontexts")
     * @Route("/matching-contexts")
     * @Method("GET")
     */
    public function __invoke(Request $request): JsonResponse
    {
        $contributors = $request->get('contributors', null);
        if (!empty($contributors) && \is_string($contributors)) {
            $contributors = explode(',', rtrim(ltrim($contributors, '['), ']'));
        }
        $matchingContexts = $this->repository->findAllPublicMatchingContext($contributors);

        if (!\is_array($matchingContexts)) {
            throw new NotFoundHttpException('No matching contexts exists');
        }

        return $this->createResponse($matchingContexts);
    }
}
