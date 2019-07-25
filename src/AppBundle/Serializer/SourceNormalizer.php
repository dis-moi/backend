<?php

namespace AppBundle\Serializer;

use AppBundle\Entity\Source;
use AppBundle\Helper\DataConverter;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;

class SourceNormalizer implements NormalizerInterface, NormalizerAwareInterface
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
        return $data instanceof Source;
    }

    public function normalize($object, $format = null, array $context = array()) : array
    {
        if (!($object instanceof Source)) throw new InvalidArgumentException();

        $url = $object->getUrl();
        return [
            'label' => $object->getLabel(),
            'url' => strlen($url) ? $url : ''
        ];
    }
}
