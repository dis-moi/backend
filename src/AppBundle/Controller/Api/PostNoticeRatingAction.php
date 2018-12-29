<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Rating;
use AppBundle\Repository\NoticeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\SerializerInterface;

class PostNoticeRatingAction extends BaseAction
{
    protected $noticeRepository;
    protected $entityManager;

    public function __construct(SerializerInterface $serializer, NoticeRepository $noticeRepository, EntityManagerInterface $entityManager)
    {
        parent::__construct($serializer);
        $this->noticeRepository = $noticeRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/notices/{id}/ratings")
     * @Method("POST")
     */
    public function __invoke(Request $request)
    {
        $id = $request->get('id', null);
        $notice = $this->noticeRepository->getOne($id);

        if (!$notice) {
            throw new NotFoundHttpException('Notice not found.');
        }

        $rating = $this->serializer->deserialize($request->getContent(), Rating::class, 'json', ['notice' => $notice]);

        $this->entityManager->persist($rating);
        $this->entityManager->flush();

        return new JsonResponse('', 204, [], true);
    }
}
