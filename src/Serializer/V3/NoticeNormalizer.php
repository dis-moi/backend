<?php

declare(strict_types=1);

namespace App\Serializer\V3;

use App\Domain\Service\MessagePresenter;
use App\Domain\Service\NoticeUrlGenerator;
use App\Entity\Contributor;
use App\Entity\Notice;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class NoticeNormalizer extends EntityWithImageNormalizer implements ContextAwareNormalizerInterface, NormalizerAwareInterface
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
     * @param mixed   $data
     * @param string  $format
     * @param mixed[] $context
     */
    public function supportsNormalization($data, $format = null, $context = []): bool
    {
        $version = $context[NormalizerOptions::VERSION] ?? null;

        return $data instanceof Notice && 3 === $version;
    }

    /**
     * @param mixed[]    $context
     * @param mixed|null $format
     * @param mixed      $notice
     *
     * @return mixed[]
     */
    public function normalize($notice, $format = null, array $context = []): array
    {
        if ( ! ($notice instanceof Notice)) {
            throw new InvalidArgumentException();
        }

        $base = [
            'id' => $notice->getId(),
            'url' => $this->noticeUrlGenerator->generate($notice),
            'message' => $this->messagePresenter->present($notice->getMessage()),
            'strippedMessage' => $this->messagePresenter->strip($notice->getMessage()),
            'visibility' => $notice->getVisibility()->getValue(),
            'exampleUrl' => $notice->getExampleUrl(), // @deprecated
            'exampleMatchingUrl' => $notice->getExampleMatchingUrl(),
            'screenshot' => $this->getImageAbsoluteUrl($notice, 'screenshotFile'),
            'ratings' => [
                'likes' => $notice->getLikedRatingCount(),
                'dislikes' => $notice->getDislikedRatingCount(),
            ],
            'created' => self::formatDateTime($notice->getCreated()),
            'modified' => self::formatDateTime($notice->getUpdated()),
        ];

        if ($context[NormalizerOptions::INCLUDE_CONTRIBUTORS_DETAILS] ?? false) {
            $base['contributor'] = $this->normalizer->normalize($notice->getContributor(), $format, $context);
            $base['relayers'] = $notice->getRelayers()->map(function (Contributor $contributor) use ($format, $context) {
                return $this->normalizer->normalize($contributor, $format, $context);
            })->toArray();
        } else {
            $base['contributorId'] = $notice->getContributor() ? $notice->getContributor()->getId() : null;
            $base['relayersIds'] = $notice->getRelayers()->map(static function (Contributor $contributor) {
                return $contributor->getId();
            })->toArray();
        }

        return $base;
    }

    public static function formatDateTime(\DateTime $datetime): string
    {
        return $datetime->format('c');
    }
}
