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

    /**
     * @return bool
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof MatchingContext;
    }

    /**
     * @param MatchingContext $object
     *
     * @return array
     */
    public function normalize($object, $format = null, array $context = array())
    {
        return [
            'notice_url' => $this->router->generate(
                'app_api_getnoticeaction__invoke',
                [ 'id' => $object->getNotice()->getId() ],
                RouterInterface::ABSOLUTE_URL),
            'url_regex' => $object->getUrlRegex(),
            'exclude_url_regex' => $object->getExcludeUrlRegex()
        ];
    }
}
