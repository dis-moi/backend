<?php

declare(strict_types=1);

namespace App\Serializer;

use App\Domain\Service\MessagePresenter;
use App\Domain\Service\NoticeUrlGenerator;
use App\Entity\Contributor;
use App\Entity\Notice;
use App\Serializer\Serializable\Picture;
use App\Serializer\Serializable\Thumb;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class ContributorNormalizer extends EntityWithImageNormalizer implements NormalizerInterface, NormalizerAwareInterface
{
    /**
     * @var NormalizerInterface
     */
    protected $normalizer;

    /**
     * @var NoticeUrlGenerator
     */
    protected $noticeUrlGenerator;

    /**
     * @var MessagePresenter
     */
    private $messagePresenter;

    public function __construct(NoticeUrlGenerator $noticeUrlGenerator, MessagePresenter $messagePresenter, UploaderHelper $uploader, RequestStack $requestStack)
    {
        parent::__construct($uploader, $requestStack);
        $this->noticeUrlGenerator = $noticeUrlGenerator;
        $this->messagePresenter = $messagePresenter;
    }

    /**
     * Sets the owning Normalizer object.
     */
    public function setNormalizer(NormalizerInterface $normalizer): void
    {
        $this->normalizer = $normalizer;
    }

    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof Contributor;
    }

    /**
     * Normalizes an object into a set of arrays/scalars.
     *
     * @param mixed   $contributor Contributor to normalize
     * @param ?string $format      Format the normalization result will be encoded as
     * @param array   $context     Context options for the normalizer
     *
     * @throws InvalidArgumentException   Occurs when the object given is not a supported type for the normalizer
     * @throws CircularReferenceException Occurs when the normalizer detects a circular reference when no circular
     *                                    reference handler can fix it
     * @throws LogicException             Occurs when the normalizer is not called in an expected context
     * @throws ExceptionInterface         Occurs for all the other cases of errors
     */
    public function normalize($contributor, $format = null, array $context = []): array
    {
        if (!($contributor instanceof Contributor)) {
            throw new InvalidArgumentException('The normalized object must be of type Contributor');
        }
        $exampleNotice = $contributor->getTheirMostLikedOrDisplayedNotice();
        $exampleNoticeMatchingContexts = $exampleNotice ? $exampleNotice->getMatchingContexts() : null;
        $relays = $contributor->getPublicRelays();

        return [
            'id' => $contributor->getId(),
            'name' => $contributor->getName(),
            'title' => $contributor->getTitle(),
            'website' => $contributor->getWebsite(),
            'intro' => $contributor->getIntro() ? $this->messagePresenter->present($contributor->getIntro()) : null,
            'avatar' => !empty($contributor->getImage()) ?
                $this->normalizer->normalize(self::avatarWithThumbs($contributor), $format, $context) :
                null,
            'banner' => $this->getImageAbsoluteUrl($contributor, 'bannerImageFile'),
            'preview' => $this->getImageAbsoluteUrl($contributor, 'previewImageFile'),
            'contributions' => $contributor->getNoticesCount(),
            'contribution' => [
                'example' => $exampleNotice && $exampleNoticeMatchingContexts ? [/* Deprecated */
                    'matchingUrl' => $exampleNoticeMatchingContexts->first() ? $exampleNoticeMatchingContexts->first()->getExampleUrl() : null,
                    'noticeId' => $exampleNotice->getId(),
                    'noticeUrl' => $this->noticeUrlGenerator->generate($exampleNotice),
                    'screenshot' => $this->getImageAbsoluteUrl($exampleNotice, 'screenshotFile'),
                ] : null,
                'starred' => $exampleNotice && $exampleNoticeMatchingContexts ? [
                    'matchingUrl' => $exampleNoticeMatchingContexts->first() ? $exampleNoticeMatchingContexts->first()->getExampleUrl() : null,
                    'noticeId' => $exampleNotice->getId(),
                    'noticeUrl' => $this->noticeUrlGenerator->generate($exampleNotice),
                    'screenshot' => $this->getImageAbsoluteUrl($exampleNotice, 'screenshotFile'),
                ] : null,
                'numberOfPublishedNotices' => $contributor->getNoticesCount(),
            ],
            'ratings' => [
                'subscribes' => $contributor->getActiveSubscriptionsCount(),
            ],
            'noticesUrls' => array_values($contributor->getPublicNotices()->map(function (Notice $notice) {
                return $this->noticeUrlGenerator->generate($notice);
            })->toArray()),
            'relayedNoticesUrls' => $relays ? $relays->map(function (Notice $notice) {
                return $this->noticeUrlGenerator->generate($notice);
            })->toArray() : null,
            'categories' => $contributor->getCategories(),
        ];
    }

    private static function avatarWithThumbs(Contributor $contributor): Picture
    {
        return Picture::fromContributor($contributor)
            ->addThumb(Thumb::fromName(Thumb::SMALL))
            ->addThumb(Thumb::fromName(Thumb::NORMAL))
            ->addThumb(Thumb::fromName(Thumb::LARGE))
            ->addThumb(Thumb::fromName(Thumb::EXTRA_LARGE));
    }
}
