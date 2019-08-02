<?php
namespace AppBundle\Serializer;

use AppBundle\Entity\MatchingContext;
use AppBundle\Helper\PregEscaper;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;

class MatchingContextNormalizer implements NormalizerInterface
{
    protected $router;
    protected $escaper;

    public function __construct(RouterInterface $router, PregEscaper $escaper)
    {
        $this->router = $router;
        $this->escaper = $escaper;
    }

    public function supportsNormalization($data, $format = null) : bool
    {
        return $data instanceof MatchingContext;
    }

    public function normalize($object, $format = null, array $context = array()) : array
    {
        if (!($object instanceof MatchingContext)) throw new InvalidArgumentException();

        return array_filter([
            'noticeId' => $object->getNotice()->getId(),
            'noticeUrl' => $this->router->generate(
                'app_api_getnoticeaction__invoke',
                [ 'id' => $object->getNotice()->getId() ],
                RouterInterface::ABSOLUTE_URL),
            'urlRegex' => $object->getFullUrlRegex($this->escaper),
            'excludeUrlRegex' => $object->getExcludeUrlRegex(),
            'querySelector' => $object->getQuerySelector()
        ]);
    }
}
