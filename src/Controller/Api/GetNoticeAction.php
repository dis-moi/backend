<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Repository\NoticeRepository;
use App\Serializer\NormalizerOptions;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\SerializerInterface;

class GetNoticeAction extends BaseAction
{
    /**
     * @var NoticeRepository
     */
    protected $repository;

    public function __construct(SerializerInterface $serializer, NoticeRepository $repository)
    {
        parent::__construct($serializer);

        $this->repository = $repository;
    }

    /**
     * @Route("/notices/{id}")
     * @Method("GET")
     */
    public function __invoke(Request $request): JsonResponse
    {
        $id = $request->get('id', null);
        $notice = $this->repository->getOne((int) $id);

        if (!$notice) {
            throw new NotFoundHttpException('Notice not found.');
        }

        return $this->createResponse($notice, [NormalizerOptions::INCLUDE_CONTRIBUTORS_DETAILS => true]);
    }
}
