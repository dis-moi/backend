<?php
namespace AppBundle\Controller;

use AppBundle\Repository\MatchingContextRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\SerializerInterface;

class AdminApiController
{
    protected $repository;
    protected $serializer;

    public function __construct(SerializerInterface $serializer, MatchingContextRepository $repository)
    {
        $this->serializer = $serializer;

        $this->repository = $repository;
    }

    /**
     * @Route("/matchingcontexts")
     */
    public function getMatchingcontextsAction()
    {
        $matchingContexts = $this->repository->findAllWithPrivateVisibility();

        if (!$matchingContexts) {
            throw new NotFoundHttpException('No matching contexts exists');
        }

        $json = $this->serializer->serialize($matchingContexts, 'json', ['groups' => [ 'v3:list' ]]);

        return new JsonResponse($json, 200, [], true);
    }
}
