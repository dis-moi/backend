<?php

declare(strict_types=1);

namespace App\Serializer\V3;

use App\Entity\MatchingContext;
use App\Helper\PregEscaper;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class MatchingContextNormalizer implements ContextAwareNormalizerInterface, NormalizerAwareInterface
{
    /**
     * @var NormalizerInterface
     */
    protected $normalizer;

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

    public function setNormalizer(NormalizerInterface $normalizer): void
    {
        $this->normalizer = $normalizer;
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
            'domains' => $matchingContext->getDomains(),
            'noticeId' => $matchingContext->getNotice()->getId(),
            'noticeUrl' => $this->router->generate(
                'app_api_v3_getnoticeaction__invoke',
                ['id' => $matchingContext->getNotice()->getId()],
                RouterInterface::ABSOLUTE_URL),
            // @todo should be renamed `fullUrlRegex` in next major release
            'urlRegex' => $matchingContext->getFullUrlRegex($this->escaper),
            // @todo should probably be named `urlRegex` in next major release
            'urlPathRegex' => $matchingContext->getUrlRegex(),
            'excludeUrlRegex' => $matchingContext->getCompleteExcludeUrlRegex(),
            'querySelector' => $matchingContext->getQuerySelector(),
            'xpath' => $matchingContext->getXpath(),
            'product' => $this->normalizer->normalize($matchingContext->getProduct(), $format, $context),
        ]);
    }
}
