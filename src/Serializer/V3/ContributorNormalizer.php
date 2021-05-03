<?php

declare(strict_types=1);

namespace App\Serializer\V3;

use App\Domain\Service\MessagePresenter;
use App\Domain\Service\NoticeUrlGenerator;
use App\Entity\Contributor;
use App\Entity\Notice;
use App\Serializer\Serializable\Picture;
use App\Serializer\Serializable\Thumb;
use LogicException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Exception\CircularReferenceException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

/**
 * Class ContributorNormalizer.
 */
class ContributorNormalizer extends EntityWithImageNormalizer implements ContextAwareNormalizerInterface, NormalizerAwareInterface
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

    /**
     * Checks whether the given class is supported for normalization by this normalizer.
     *
     * @param mixed         $data    Data to normalize
     * @param string | null $format  The format being (de-)serialized from or into
     * @param mixed[]       $context
     */
    public function supportsNormalization($data, $format = null, $context = []): bool
    {
        $version = $context[NormalizerOptions::VERSION] ?? null;

        return $data instanceof Contributor && 3 === $version;
    }

    private static function avatarWithThumbs(Contributor $contributor): Picture
    {
        return Picture::fromContributor($contributor)
            ->addThumb(Thumb::fromName(Thumb::SMALL))
            ->addThumb(Thumb::fromName(Thumb::NORMAL))
            ->addThumb(Thumb::fromName(Thumb::LARGE))
            ->addThumb(Thumb::fromName(Thumb::EXTRA_LARGE));
    }

    /**
     * Normalizes an object into a set of arrays/scalars.
     *
     * @param mixed   $contributor Contributor to normalize
     * @param string  $format      Format the normalization result will be encoded as
     * @param mixed[] $context     Context options for the normalizer
     *
     * @throws InvalidArgumentException   Occurs when the object given is not a supported type for the normalizer
     * @throws CircularReferenceException Occurs when the normalizer detects a circular reference when no circular
     *                                    reference handler can fix it
     * @throws LogicException             Occurs when the normalizer is not called in an expected context
     * @throws ExceptionInterface         Occurs for all the other cases of errors
     *
     * @return mixed[]
     */
    public function normalize($contributor, $format = null, array $context = []): array
    {
        if ( ! ($contributor instanceof Contributor)) {
            throw new InvalidArgumentException('The normalized object must be of type Contributor');
        }
        $context[NormalizerOptions::INCLUDE_CONTRIBUTORS_DETAILS] = false;
        $pinnedNotices = $contributor->getPinnedNotices();
        $exampleNotice = $pinnedNotices->first() ?: $contributor->getPublicNotices()->first();
        $relays = $contributor->getPublicRelays();

        return [
            'id' => $contributor->getId(),
            'name' => $contributor->getName(),
            'title' => $contributor->getTitle(),
            'website' => $contributor->getWebsite(),
            'intro' => $contributor->getIntro() ? $this->messagePresenter->present($contributor->getIntro()) : null,
            'avatar' => ! empty($contributor->getImage()) ?
                $this->normalizer->normalize(self::avatarWithThumbs($contributor), $format, $context) :
                null,
            'banner' => $this->getImageAbsoluteUrl($contributor, 'bannerImageFile'),
            'preview' => $this->getImageAbsoluteUrl($contributor, 'previewImageFile'),
            'contributions' => $contributor->getNoticesCount(),
            'contribution' => [
                'example' => $this->normalizer->normalize($exampleNotice, $format, $context),
                'pinnedNotices' => $pinnedNotices->map(function (Notice $notice) use ($context, $format) {
                    return $this->normalizer->normalize($notice, $format, $context);
                })->toArray(),
                'numberOfPublishedNotices' => $contributor->getNoticesCount(),
            ],
            'ratings' => [
                'subscribes' => $contributor->getActiveSubscriptionsCount(),
            ],
            'noticesUrls' => array_values($contributor->getPublicNotices()->map(function (Notice $notice) {
                return $this->noticeUrlGenerator->generate($notice);
            })->toArray()),
            'relayedNoticesUrls' => $relays->map(function (Notice $notice) {
                return $this->noticeUrlGenerator->generate($notice);
            })->toArray(),
            'categories' => $contributor->getCategories(),
        ];
    }
}
