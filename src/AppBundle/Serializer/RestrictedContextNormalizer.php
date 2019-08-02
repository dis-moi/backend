<?php
namespace AppBundle\Serializer;

use AppBundle\Entity\RestrictedContext;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;

class RestrictedContextNormalizer implements NormalizerInterface
{

    public function supportsNormalization($data, $format = null) : bool
    {
        return $data instanceof RestrictedContext;
    }

    public function normalize($object, $format = null, array $context = array()) : array
    {
        if (!($object instanceof RestrictedContext)) throw new InvalidArgumentException();

        return [
            'urlRegex' => $object->getUrlRegex()
        ];
    }
}
