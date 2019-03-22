<?php
namespace AppBundle\Serializer;

use AppBundle\Entity\RestrictedContext;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class RestrictedContextNormalizer implements NormalizerInterface
{
    /** @var RouterInterface $router */
    protected $router;

    /**
     * RestrictedContextNormalizer constructor.
     *
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function supportsNormalization($data, $format = null) : bool
    {
        return $data instanceof RestrictedContext;
    }

    public function normalize($object, $format = null, array $context = array()) : array
    {
        return [
            'urlRegex' => $object->getUrlRegex()
        ];
    }
}
