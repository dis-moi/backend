<?php

namespace AppBundle\Serializer;

use AppBundle\Entity\Contributor;
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
     * Sets the owning Normalizer object.
     *
     */
    public function setNormalizer(NormalizerInterface $normalizer)
    {
        $this->normalizer = $normalizer;
    }

    public function supportsNormalization($data, $format = null) : bool
    {
        return $data instanceof Contributor;
    }

    public function normalize($object, $format = null, array $context = array()) : array
    {
        if (!($object instanceof Contributor)) throw new InvalidArgumentException();

        return [
            'contributions' => $object->getNoticesCount(),
            'id' => $object->getId(),
            'intro' => $object->getIntro(),
            'name' => $object->getName(),
        ];
    }
}
