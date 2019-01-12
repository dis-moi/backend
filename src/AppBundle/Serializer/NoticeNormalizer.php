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
     * @param NormalizerInterface $normalizer
     */
    public function setNormalizer(NormalizerInterface $normalizer)
    {
        $this->normalizer = $normalizer;
    }

    /**
     * @return bool
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Notice;
    }

    /**
     * @param Notice $object
     * @return array
     */
    public function normalize($object, $format = null, array $context = array())
    {
        return [
            'id' => $object->getId(),
            'visibility' => $object->getVisibility()->getValue(),
            'message' => DataConverter::convertFullMessage($object->getMessage()),
            'contributor' => $this->normalizer->normalize($object->getContributor(), $format, $context),
            'type' => $this->normalizer->normalize($object->getType(), $format, $context),

            'source' => $this->normalizer->normalize($object->getSource(), $format, $context),
            'ratings' => [
                'approves' => $object->getApprovedRatingCount(),
                'dislikes' => $object->getDismissedRatingCount()
            ]
        ];
    }

    protected function updateSourceHref($href)
    {
        return strlen($href)
            ? DataConverter::addUtmSourceToLink($href)
            : ''
        ;
    }
}
