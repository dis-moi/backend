<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\Rating;
use App\Repository\NoticeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Serializer\SerializerInterface;

class PostNoticeRatingAction extends BaseAction
{
    /**
     * @var NoticeRepository
     */
    protected $noticeRepository;

    /**
     * @var EntityManagerInterface
     */
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
    public function __invoke(Request $request): JsonResponse
    {
        $id = $request->get('id', null);
        $notice = $this->noticeRepository->getOne((int) $id);

        if (!$notice) {
            throw new NotFoundHttpException('Notice not found.');
        }

        try {
            $rating = $this->serializer->deserialize($request->getContent(), Rating::class, 'json', ['notice' => $notice]);
        } catch (\Exception $e) {
            throw new UnprocessableEntityHttpException($e->getMessage(), $e);
        }

        $this->entityManager->persist($rating);
        $this->entityManager->flush();

        return new JsonResponse('', 204, [], true);
    }
}
