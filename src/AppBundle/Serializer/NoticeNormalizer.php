<?php

namespace AppBundle\Serializer;

use AppBundle\Entity\Notice;
use Domain\Service\MessagePresenter;
use Domain\Service\NoticeUrlGenerator;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class NoticeNormalizer extends EntityWithImageNormalizer implements NormalizerInterface, NormalizerAwareInterface
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
        return $data instanceof Notice;
    }

    public function normalize($notice, $format = null, array $context = []): array
    {
        if (!($notice instanceof Notice)) {
            throw new InvalidArgumentException();
        }

        $base = [
            'id' => $notice->getId(),
            'url' => $this->noticeUrlGenerator->generate($notice),
            'message' => $this->messagePresenter->present($notice->getMessage()),
            'visibility' => $notice->getVisibility()->getValue(),
            'exampleUrl' => $notice->getExampleUrl(),
            'screenshot' => $this->getImageAbsoluteUrl($notice, 'screenshotFile'),
            'ratings' => [
                'likes' => $notice->getLikedRatingCount(),
                'dislikes' => $notice->getDislikedRatingCount(),
            ],
            'created' => $this->formatDateTime($notice->getCreated()),
            'modified' => $this->formatDateTime($notice->getUpdated()),
        ];

        if ($context[NormalizerOptions::INCLUDE_CONTRIBUTORS_DETAILS]) {
            $base['contributor'] = $this->normalizer->normalize($notice->getContributor(), $format, $context);
        } else {
            $base['contributorId'] = $notice->getContributor()->getId();
        }

        return $base;
    }

    protected function formatDateTime(\DateTime $datetime): string
    {
        return $datetime->format('c');
    }
}
