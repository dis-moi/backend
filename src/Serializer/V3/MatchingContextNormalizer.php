<?php

declare(strict_types=1);

namespace App\Serializer\V3;

use App\Entity\MatchingContext;
use App\Helper\PregEscaper;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;

class MatchingContextNormalizer implements ContextAwareNormalizerInterface
{
    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var PregEscaper
     */
    protected $escaper;

    public function __construct(RouterInterface $router, PregEscaper $escaper)
    {
        $this->router = $router;
        $this->escaper = $escaper;
    }

    /**
     * @param mixed   $data
     * @param string  $format
     * @param mixed[] $context
     */
    public function supportsNormalization($data, $format = null, $context = []): bool
    {
        $version = $context[NormalizerOptions::VERSION] ?? null;

        return $data instanceof MatchingContext;
    }

    /**
     * @param mixed[]    $context
     * @param mixed|null $format
     * @param mixed      $matchingContext
     *
     * @return mixed[]
     */
    public function normalize($matchingContext, $format = null, array $context = []): array
    {
        if ( ! ($matchingContext instanceof MatchingContext)) {
            throw new InvalidArgumentException();
        }

        return array_filter([
            'id' => $matchingContext->getId(),
            'noticeId' => $matchingContext->getNotice()->getId(),
            'noticeUrl' => $this->router->generate(
                'app_api_v3_getnoticeaction__invoke',
                ['id' => $matchingContext->getNotice()->getId()],
                RouterInterface::ABSOLUTE_URL),
            'urlRegex' => $matchingContext->getFullUrlRegex($this->escaper),
            'excludeUrlRegex' => $matchingContext->getCompleteExcludeUrlRegex(),
            'querySelector' => $matchingContext->getQuerySelector(),
            'xpath' => $matchingContext->getXpath(),
        ]);
    }
}
