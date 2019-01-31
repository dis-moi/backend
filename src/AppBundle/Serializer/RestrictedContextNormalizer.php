<?php
namespace AppBundle\Serializer;

use AppBundle\Entity\RestrictedContext;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class RestrictedContextNormalizer implements NormalizerInterface
{
    protected $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @return bool
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof RestrictedContext;
    }

    /**
     * @param MatchingContext $object
     *
     * @return array
     */
    public function normalize($object, $format = null, array $context = array())
    {
        return [
            'url_regex' => $object->getUrlRegex()
        ];
    }
}
