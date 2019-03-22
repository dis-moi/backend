<?php
namespace AppBundle\Serializer;

use AppBundle\Entity\MatchingContext;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class MatchingContextNormalizer implements NormalizerInterface
{
    protected $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function supportsNormalization($data, $format = null) : bool
    {
        return $data instanceof MatchingContext;
    }

    public function normalize($object, $format = null, array $context = array()) : array
    {
        return [
            'noticeId' => $object->getNotice()->getId(),
            'noticeUrl' => $this->router->generate(
                'app_api_getnoticeaction__invoke',
                [ 'id' => $object->getNotice()->getId() ],
                RouterInterface::ABSOLUTE_URL),
            'urlRegex' => $object->getUrlRegex(),
            'excludeUrlRegex' => $object->getExcludeUrlRegex(),
            'querySelector' => $object->getQuerySelector()
        ];
    }
}
