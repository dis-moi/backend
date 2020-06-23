<?php

namespace AppBundle\Serializer;

use AppBundle\Entity\Contributor;
use AppBundle\Entity\Notice;
use AppBundle\Serializer\Serializable\Picture;
use AppBundle\Serializer\Serializable\Thumb;
use Domain\Service\MessagePresenter;
use Domain\Service\NoticeUrlGenerator;
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
    public function setNormalizer(NormalizerInterface $normalizer)
    {
        $this->normalizer = $normalizer;
    }

    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof Contributor;
    }

    public function normalize($contributor, $format = null, array $context = []): array
    {
        if (!($contributor instanceof Contributor)) {
            throw new InvalidArgumentException();
        }
        $exampleNotice = $contributor->getTheirMostLikedOrDisplayedNotice();

        return [
            'id' => $contributor->getId(),
            'name' => $contributor->getName(),
            'website' => $contributor->getWebsite(),
            'intro' => $contributor->getIntro() ? $this->messagePresenter->present($contributor->getIntro()) : null,
            'avatar' => !empty($contributor->getImage()) ?
                $this->normalizer->normalize(self::avatarWithThumbs($contributor), $format, $context) :
                null,
            'banner' => $this->getImageAbsoluteUrl($contributor, 'bannerImageFile'),
            'contributions' => $contributor->getNoticesCount(),
            'contribution' => [
                'example' => [/* Deprecated */
                    'matchingUrl' => $exampleNotice->getMatchingContexts()->first() ? $exampleNotice->getMatchingContexts()->first()->getExampleUrl() : null,
                    'noticeId' => $exampleNotice->getId(),
                    'noticeUrl' => $this->noticeUrlGenerator->generate($exampleNotice),
                    'screenshot' => $this->getImageAbsoluteUrl($exampleNotice, 'screenshotFile'),
                ],
                'starred' => [
                    'matchingUrl' => $exampleNotice->getMatchingContexts()->first() ? $exampleNotice->getMatchingContexts()->first()->getExampleUrl() : null,
                    'noticeId' => $exampleNotice->getId(),
                    'noticeUrl' => $this->noticeUrlGenerator->generate($exampleNotice),
                    'screenshot' => $this->getImageAbsoluteUrl($exampleNotice, 'screenshotFile'),
                ],
                'numberOfPublishedNotices' => $contributor->getNoticesCount(),
            ],
            'ratings' => [
                'subscribes' => $contributor->getActiveSubscriptionsCount(),
            ],
            'noticesUrls' => array_values($contributor->getPublicNotices()->map(function (Notice $notice) {
                return $this->noticeUrlGenerator->generate($notice);
            })->toArray()),
        ];
    }

    private static function avatarWithThumbs(Contributor $contributor): Picture
    {
        return Picture::fromContributor($contributor)
            ->addThumb(Thumb::fromName(Thumb::SMALL))
            ->addThumb(Thumb::fromName(Thumb::NORMAL))
            ->addThumb(Thumb::fromName(Thumb::LARGE));
    }
}
