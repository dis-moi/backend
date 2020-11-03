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
            $noticeLikedDisplayed->setPinnedSort(0),
            $noticeEcology->setPinnedSort(1),
            $noticeLiked->setPinnedSort(2),
            $noticeDisplayed->setPinnedSort(3),
        ]));

        $newSelection = $contributor->getPinnedNotices();
        self::assertCount(4, $newSelection);

        self::assertEquals(
            0,
            CollectionHelper::find($newSelection, Notice::equals($noticeLikedDisplayed))->getPinnedSort()
        );
        self::assertEquals(
            1,
            CollectionHelper::find($newSelection, Notice::equals($noticeEcology))->getPinnedSort()
        );
        self::assertEquals(
            2,
            CollectionHelper::find($newSelection, Notice::equals($noticeLiked))->getPinnedSort()
        );
        self::assertEquals(
            3,
            CollectionHelper::find($newSelection, Notice::equals($noticeDisplayed))->getPinnedSort()
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
            $noticeLikedDisplayed->setPinnedSort(0),
            $noticeLiked->setPinnedSort(1),
        ]));

        $newSelection = $contributor->getPinnedNotices();
        self::assertCount(2, $newSelection);

        self::assertEquals(
            0,
            CollectionHelper::find($newSelection, Notice::equals($noticeLikedDisplayed))->getPinnedSort()
        );
        self::assertEquals(
            1,
            CollectionHelper::find($newSelection, Notice::equals($noticeLiked))->getPinnedSort()
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
            $noticeLikedDisplayed->setPinnedSort(0),
            $noticeLiked->setPinnedSort(2),
            $noticeDisplayed->setPinnedSort(1),
        ]));

        $newSelection = $contributor->getPinnedNotices();
        self::assertCount(3, $newSelection);

        self::assertEquals(
            0,
            CollectionHelper::find($newSelection, Notice::equals($noticeLikedDisplayed))->getPinnedSort()
        );
        self::assertEquals(
            1,
            CollectionHelper::find($newSelection, Notice::equals($noticeDisplayed))->getPinnedSort()
        );
        self::assertEquals(
            2,
            CollectionHelper::find($newSelection, Notice::equals($noticeLiked))->getPinnedSort()
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
            $noticeLikedDisplayed->setPinnedSort(0),
            $noticeLiked->setPinnedSort(1),
            $noticeDisplayed->setPinnedSort(2),
            $noticeEcology->setPinnedSort(3),
            $noticeEcologyAndPolitics->setPinnedSort(4),
            $noticeOther->setPinnedSort(5),
        ]));
    }
}
