<?php

namespace App\Serializer;

use App\Entity\MatchingContext;
use App\Helper\PregEscaper;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class MatchingContextNormalizer implements NormalizerInterface
{
    protected $router;
    protected $escaper;

    public function __construct(RouterInterface $router, PregEscaper $escaper)
    {
        $this->router = $router;
        $this->escaper = $escaper;
    }

    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof MatchingContext;
    }

    public function normalize($matchingContext, $format = null, array $context = []): array
    {
        if (!($matchingContext instanceof MatchingContext)) {
            throw new InvalidArgumentException();
        }

        return array_filter([
            'id' => $matchingContext->getId(),
            'noticeId' => $matchingContext->getNotice()->getId(),
            'noticeUrl' => $this->router->generate(
                'app_api_getnoticeaction__invoke',
                ['id' => $matchingContext->getNotice()->getId()],
                RouterInterface::ABSOLUTE_URL),
            'urlRegex' => $matchingContext->getFullUrlRegex($this->escaper),
            'excludeUrlRegex' => $matchingContext->getCompleteExcludeUrlRegex(),
            'querySelector' => $matchingContext->getQuerySelector(),
            'xpath' => $matchingContext->getXpath(),
        ]);
    }
}
