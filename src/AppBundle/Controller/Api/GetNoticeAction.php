<?php

namespace AppBundle\Controller\Api;

use AppBundle\Repository\NoticeRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\SerializerInterface;

class GetNoticeAction extends BaseAction
{
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
    public function __invoke(Request $request)
    {
        $id = $request->get('id', null);
        $notice = $this->repository->getOne($id);

        if (!$notice) {
            throw new NotFoundHttpException('Notice not found.');
        }

        return $this->createResponse($notice);
    }
}
