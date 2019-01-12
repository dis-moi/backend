<?php

namespace AppBundle\Serializer;

use AppBundle\Entity\Notice;
use AppBundle\Entity\Source;
use AppBundle\Helper\DataConverter;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class SourceNormalizer implements NormalizerInterface, NormalizerAwareInterface
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
        return $data instanceof Source;
    }

    /**
     * @param Source $object
     * @return array
     */
    public function normalize($object, $format = null, array $context = array())
    {
        $url = $object->getUrl();
        return [
            'label' => $object->getLabel(),
            'url' => strlen($url) ? DataConverter::addUtmSourceToLink($url) : ''
        ];
    }
}
