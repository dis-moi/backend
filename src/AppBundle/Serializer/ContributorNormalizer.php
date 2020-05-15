<?php

namespace AppBundle\Serializer;

use AppBundle\Entity\Contributor;
use AppBundle\Entity\Notice;
use AppBundle\Serializer\Serializable\Picture;
use AppBundle\Serializer\Serializable\Thumb;
use Domain\Service\MessagePresenter;
use Domain\Service\NoticeUrlGenerator;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ContributorNormalizer implements NormalizerInterface, NormalizerAwareInterface
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

    public function __construct(NoticeUrlGenerator $noticeUrlGenerator, MessagePresenter $messagePresenter)
    {
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

    public function normalize($object, $format = null, array $context = []): array
    {
        if (!($object instanceof Contributor)) {
            throw new InvalidArgumentException();
        }
        $exampleNotice = $object->getTheirMostLikedOrDisplayedNotice();

        return [
            'avatar' => !empty($object->getImage()) ?
                $this->normalizer->normalize(self::avatarWithThumbs($object), $format, $context) :
                null,
            'contributions' => $object->getNoticesCount(),
            'contribution' => [
                'example' => [
                    'matchingUrl' => $exampleNotice->getMatchingContexts()->first()->getExampleUrl(),
                    'noticeId' => $exampleNotice->getId(),
                    'noticeUrl' => $this->noticeUrlGenerator->generate($exampleNotice),
                ],
                'numberOfPublishedNotices' => $object->getNoticesCount(),
            ],
            'id' => $object->getId(),
            'website' => $object->getWebsite(),
            'intro' => $object->getIntro() ? $this->messagePresenter->present($object->getIntro()) : null,
            'name' => $object->getName(),
            'ratings' => [
                'subscribes' => $object->getActiveSubscriptionsCount(),
            ],
            'noticesUrls' => array_values($object->getPublicNotices()->map(function (Notice $notice) {
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
