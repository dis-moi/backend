<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Contributor;
use App\Entity\Notice;
use App\Helper\CollectionHelper;
use App\Tests\FixtureAwareWebTestCase;
use Doctrine\Common\Collections\ArrayCollection;
use InvalidArgumentException;

/**
 * Class ContributorTest.
 */
class ContributorTest extends FixtureAwareWebTestCase
{
    public function testItAddsPinnedNotice(): void
    {
        /** @var Contributor $contributor */
        $contributor = $this->referenceRepository->getReference('famous_contributor');

        /** @var Notice $noticeEcology */
        $noticeEcology = $this->referenceRepository->getReference('notice_type_ecology_and_politics');
        /** @var Notice $noticeDisplayed */
        $noticeDisplayed = $this->referenceRepository->getReference('notice_displayed');
        /** @var Notice $noticeLiked */
        $noticeLiked = $this->referenceRepository->getReference('notice_liked');
        /** @var Notice $noticeLikedDisplayed */
        $noticeLikedDisplayed = $this->referenceRepository->getReference('notice_liked_displayed');

        self::assertCount(3, $contributor->getPinnedNotices());

        $contributor->setPinnedNotices(new ArrayCollection([
            $noticeLikedDisplayed->setPinnedRank(0),
            $noticeEcology->setPinnedRank(1),
            $noticeLiked->setPinnedRank(2),
            $noticeDisplayed->setPinnedRank(3),
        ]));

        $newSelection = $contributor->getPinnedNotices();
        self::assertCount(4, $newSelection);

        self::assertEquals(
            0,
            CollectionHelper::find($newSelection, Notice::equals($noticeLikedDisplayed))->getPinnedRank()
        );
        self::assertEquals(
            1,
            CollectionHelper::find($newSelection, Notice::equals($noticeEcology))->getPinnedRank()
        );
        self::assertEquals(
            2,
            CollectionHelper::find($newSelection, Notice::equals($noticeLiked))->getPinnedRank()
        );
        self::assertEquals(
            3,
            CollectionHelper::find($newSelection, Notice::equals($noticeDisplayed))->getPinnedRank()
        );
    }

    public function testItRemovesPinnedNotice(): void
    {
        /** @var Contributor $contributor */
        $contributor = $this->referenceRepository->getReference('famous_contributor');

        /** @var Notice $noticeLiked */
        $noticeLiked = $this->referenceRepository->getReference('notice_liked');
        /** @var Notice $noticeLikedDisplayed */
        $noticeLikedDisplayed = $this->referenceRepository->getReference('notice_liked_displayed');

        self::assertCount(3, $contributor->getPinnedNotices());

        $contributor->setPinnedNotices(new ArrayCollection([
            $noticeLikedDisplayed->setPinnedRank(0),
            $noticeLiked->setPinnedRank(1),
        ]));

        $newSelection = $contributor->getPinnedNotices();
        self::assertCount(2, $newSelection);

        self::assertEquals(
            0,
            CollectionHelper::find($newSelection, Notice::equals($noticeLikedDisplayed))->getPinnedRank()
        );
        self::assertEquals(
            1,
            CollectionHelper::find($newSelection, Notice::equals($noticeLiked))->getPinnedRank()
        );
    }

    public function testItReorderPinnedNotice(): void
    {
        /** @var Contributor $contributor */
        $contributor = $this->referenceRepository->getReference('famous_contributor');

        /** @var Notice $noticeDisplayed */
        $noticeDisplayed = $this->referenceRepository->getReference('notice_displayed');
        /** @var Notice $noticeLiked */
        $noticeLiked = $this->referenceRepository->getReference('notice_liked');
        /** @var Notice $noticeLikedDisplayed */
        $noticeLikedDisplayed = $this->referenceRepository->getReference('notice_liked_displayed');

        self::assertCount(3, $contributor->getPinnedNotices());

        $contributor->setPinnedNotices(new ArrayCollection([
            $noticeLikedDisplayed->setPinnedRank(0),
            $noticeLiked->setPinnedRank(2),
            $noticeDisplayed->setPinnedRank(1),
        ]));

        $newSelection = $contributor->getPinnedNotices();
        self::assertCount(3, $newSelection);

        self::assertEquals(
            0,
            CollectionHelper::find($newSelection, Notice::equals($noticeLikedDisplayed))->getPinnedRank()
        );
        self::assertEquals(
            1,
            CollectionHelper::find($newSelection, Notice::equals($noticeDisplayed))->getPinnedRank()
        );
        self::assertEquals(
            2,
            CollectionHelper::find($newSelection, Notice::equals($noticeLiked))->getPinnedRank()
        );
    }

    public function testItAllowOnlyFivePinnedNotices(): void
    {
        /** @var Contributor $contributor */
        $contributor = $this->referenceRepository->getReference('famous_contributor');

        /** @var Notice $noticeEcology */
        $noticeEcology = $this->referenceRepository->getReference('notice_type_ecology');
        /** @var Notice $noticeEcologyAndPolitics */
        $noticeEcologyAndPolitics = $this->referenceRepository->getReference('notice_type_ecology_and_politics');
        /** @var Notice $noticeOther */
        $noticeOther = $this->referenceRepository->getReference('notice_other');
        /** @var Notice $noticeDisplayed */
        $noticeDisplayed = $this->referenceRepository->getReference('notice_displayed');
        /** @var Notice $noticeLiked */
        $noticeLiked = $this->referenceRepository->getReference('notice_liked');
        /** @var Notice $noticeLikedDisplayed */
        $noticeLikedDisplayed = $this->referenceRepository->getReference('notice_liked_displayed');

        self::assertCount(3, $contributor->getPinnedNotices());

        $this->expectException(InvalidArgumentException::class);

        $contributor->setPinnedNotices(new ArrayCollection([
            $noticeLikedDisplayed->setPinnedRank(0),
            $noticeLiked->setPinnedRank(1),
            $noticeDisplayed->setPinnedRank(2),
            $noticeEcology->setPinnedRank(3),
            $noticeEcologyAndPolitics->setPinnedRank(4),
            $noticeOther->setPinnedRank(5),
        ]));
    }
}
