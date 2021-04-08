<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Repository\ContributorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

// TODO: Remove when not used anymore
class PostContributorRatingAction extends BaseAction
{
    /**
     * @var ContributorRepository
     */
    protected $contributorRepository;

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    public function __construct(SerializerInterface $serializer, ContributorRepository $contributorRepository, EntityManagerInterface $entityManager)
    {
        parent::__construct($serializer);
        $this->contributorRepository = $contributorRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/contributors/{id}/ratings")
     * @Method("POST")
     */
    public function __invoke(Request $request): JsonResponse
    {
        return new JsonResponse('', 204, [], true);
    }
}
