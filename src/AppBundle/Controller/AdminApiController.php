<?php
namespace AppBundle\Controller;

use AppBundle\Entity\RestrictedContext;
use AppBundle\Repository\MatchingContextRepository;
use AppBundle\Repository\RestrictedContextRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\SerializerInterface;

class AdminApiController
{
    protected $repository;
    protected $restrictedContextRepository;
    protected $serializer;

    public function __construct(SerializerInterface $serializer, MatchingContextRepository $repository, RestrictedContextRepository $restrictedContextRepository)
    {
        $this->serializer = $serializer;

        $this->repository = $repository;
        $this->restrictedContextRepository = $restrictedContextRepository;
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

    /**
     * @Route("/restrictedcontexts")
     */
    public function getRestrictedcontextsAction()
    {
        $restrictedContexts = $this->restrictedContextRepository->findAll();

        if (!$restrictedContexts) {
            throw new NotFoundHttpException('No restricted contexts exists');
        }

        $json = $this->serializer->serialize($restrictedContexts, 'json', ['groups' => [ 'v3:list' ]]);

        return new JsonResponse($json, 200, [], true);
    }
}
