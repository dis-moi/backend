<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Entity\Notice;
use App\Entity\Rating;
use App\Message\NoticeRated;
use App\Repository\RatingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class NoticeRatedHandler implements MessageHandlerInterface
{
    /** @var RatingRepository */
    private $ratingRepository;
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em, RatingRepository $ratingRepository)
    {
        $this->ratingRepository = $ratingRepository;
        $this->em = $em;
    }

    public function __invoke(NoticeRated $noticeUpdated): void
    {
        $this->updateNoticeRatingsCount($noticeUpdated->getNotice());
    }

    protected function updateNoticeRatingsCount(Notice $notice): void
    {
        $notice
            ->setBadgedCount($this->ratingRepository->getNoticeRatingsCountForType($notice, Rating::BADGE))
            ->setDisplayedCount($this->ratingRepository->getNoticeRatingsCountForType($notice, Rating::DISPLAY))
            ->setUnfoldedCount($this->ratingRepository->getNoticeRatingsCountForType($notice, Rating::UNFOLD))
            ->setClickedCount($this->ratingRepository->getNoticeRatingsCountForType($notice, Rating::OUTBOUND_CLICK))
            ->setLikedCount($this->ratingRepository->getNoticeRatingsCountForType($notice, Rating::LIKE, Rating::UNLIKE))
            ->setDislikedCount($this->ratingRepository->getNoticeRatingsCountForType($notice, Rating::DISLIKE, Rating::UNDISLIKE))
            ->setDismissedCount($this->ratingRepository->getNoticeRatingsCountForType($notice, Rating::DISMISS, Rating::UNDISMISS));
        $this->em->flush();
    }
}
