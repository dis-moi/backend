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
        $limit = $request->get('limit', 1000);
        $offset = $request->get('offset', 0);

        if ($contributorId) {
            $notices = $this->repository->getPageByContributor($contributorId, $limit, $offset);
        } else {
            $notices = $this->repository->getPage($limit, $offset);
        }

        if (!is_iterable($notices)) {
            throw new NotFoundHttpException('No notices found');
        }

        return $this->createResponse($notices, [NormalizerOptions::INCLUDE_CONTRIBUTORS_DETAILS => false]);
    }
}
