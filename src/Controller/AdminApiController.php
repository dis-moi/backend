<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\MatchingContextRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\SerializerInterface;

class AdminApiController
{
    /**
     * @var MatchingContextRepository
     */
    protected $repository;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    public function __construct(SerializerInterface $serializer, MatchingContextRepository $repository)
    {
        $this->serializer = $serializer;

        $this->repository = $repository;
    }

    /**
     * @Route("/matchingcontexts")
     */
    public function getMatchingcontextsAction(): JsonResponse
    {
        $matchingContexts = $this->repository->findAllWithPrivateVisibility();

        if ( ! $matchingContexts) {
            throw new NotFoundHttpException('No matching contexts exists');
        }

        $json = $this->serializer->serialize($matchingContexts, 'json', ['groups' => ['v3:list']]);

        return new JsonResponse($json, 200, [], true);
    }
}
