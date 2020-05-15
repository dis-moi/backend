<?php

namespace AppBundle\Controller\Api;

use AppBundle\Repository\ContributorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

// TODO: Remove when not used anymore
class PostContributorRatingAction extends BaseAction
{
    protected $contributorRepository;
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
    public function __invoke(Request $request)
    {
        return new JsonResponse('', 204, [], true);
    }
}
