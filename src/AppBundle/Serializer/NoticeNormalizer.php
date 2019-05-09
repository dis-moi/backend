<?php

namespace AppBundle\Serializer;

use AppBundle\Entity\Notice;
use AppBundle\Helper\DataConverter;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class NoticeNormalizer implements NormalizerInterface, NormalizerAwareInterface
{
    /**
     * @var NormalizerInterface
     */
    protected $normalizer;

    /**
     * Sets the owning Normalizer object.
     *
     */
    public function setNormalizer(NormalizerInterface $normalizer)
    {
        $this->normalizer = $normalizer;
    }

    public function supportsNormalization($data, $format = null) : bool
    {
        return $data instanceof Notice;
    }

    public function normalize($object, $format = null, array $context = array()) : array
    {
        return [
            'contributor' => $this->normalizer->normalize($object->getContributor(), $format, $context),
            'created' => $this->formatDateTime($object->getCreated()),
            'id' => $object->getId(),
            'intention' => $object->getIntention()->getValue(),
            'message' => DataConverter::convertFullMessage($object->getMessage()),
            'modified' => $this->formatDateTime($object->getUpdated()),
            'ratings' => [
                'likes' => $object->getLikedRatingCount(),
                'dislikes' => $object->getDislikedRatingCount()
            ],
            'source' => $this->normalizer->normalize($object->getSource(), $format, $context),
            'visibility' => $object->getVisibility()->getValue(),
        ];
    }

    protected function formatDateTime(\DateTime $datetime) : string
    {
        return $datetime->format('c');
    }

    protected function updateSourceHref($href)
    {
        return strlen($href)
            ? DataConverter::addUtmSourceToLink($href)
            : ''
        ;
    }
}
