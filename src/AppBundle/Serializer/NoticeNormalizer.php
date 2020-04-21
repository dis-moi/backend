<?php

namespace AppBundle\Serializer;

use AppBundle\Entity\Notice;
use AppBundle\Helper\DataConverter;
use Domain\Service\NoticeUrlGenerator;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class NoticeNormalizer implements NormalizerInterface, NormalizerAwareInterface
{
    /**
     * @var NormalizerInterface
     */
    protected $normalizer;

    /**
     * @var NoticeUrlGenerator
     */
    protected $noticeUrlGenerator;

    public function __construct(NoticeUrlGenerator $noticeUrlGenerator)
    {
        $this->noticeUrlGenerator = $noticeUrlGenerator;
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

    public function normalize($object, $format = null, array $context = []): array
    {
        if (!($object instanceof Notice)) {
            throw new InvalidArgumentException();
        }

        return [
            'contributor' => $this->normalizer->normalize($object->getContributor(), $format, $context),
            'created' => $this->formatDateTime($object->getCreated()),
            'id' => $object->getId(),
            'url' => $this->noticeUrlGenerator->generate($object),
            'intention' => $object->getIntention()->getValue(),
            'message' => DataConverter::convertFullMessage($object->getMessage()),
            'modified' => $this->formatDateTime($object->getUpdated()),
            'ratings' => [
                'likes' => $object->getLikedRatingCount(),
                'dislikes' => $object->getDislikedRatingCount(),
            ],
            'visibility' => $object->getVisibility()->getValue(),
        ];
    }

    protected function formatDateTime(\DateTime $datetime): string
    {
        return $datetime->format('c');
    }
}
