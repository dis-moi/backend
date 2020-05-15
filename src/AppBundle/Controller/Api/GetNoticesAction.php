<?php

namespace AppBundle\Controller\Api;

use AppBundle\Repository\NoticeRepository;
use AppBundle\Serializer\NormalizerOptions;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\SerializerInterface;

class GetNoticesAction extends BaseAction
{
    /**
     * @var NoticeRepository
     */
    private $repository;

    public function __construct(SerializerInterface $serializer, NoticeRepository $repository)
    {
        parent::__construct($serializer);
        $this->repository = $repository;
    }

    /**
     * @Route("/notices")
     * @Method("GET")
     */
    public function __invoke(Request $request)
    {
        $contributorId = $request->get('contributor', null);

        if ($contributorId) {
            $notices = $this->repository->getByContributor($contributorId);
        } else {
            $notices = $this->repository->getAll();
        }

        if (!is_array($notices)) {
            throw new NotFoundHttpException('No notices found');
        }

        return $this->createResponse($notices, [NormalizerOptions::INCLUDE_CONTRIBUTORS_DETAILS => false]);
    }
}
