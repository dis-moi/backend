<?php

declare(strict_types=1);

namespace App\Serializer\V3;

use App\Entity\RestrictedContext;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;

class RestrictedContextNormalizer implements ContextAwareNormalizerInterface
{
    /**
     * @param mixed   $data
     * @param string  $format
     * @param mixed[] $context
     */
    public function supportsNormalization($data, $format = null, $context = []): bool
    {
        $version = $context[NormalizerOptions::VERSION] ?? null;

        return $data instanceof RestrictedContext && 3 === $version;
    }

    /**
     * @param string  $format
     * @param mixed[] $context
     * @param mixed   $object
     *
     * @return mixed[]
     */
    public function normalize($object, $format = null, array $context = []): array
    {
        if (!($object instanceof RestrictedContext)) {
            throw new InvalidArgumentException();
        }

        return [
            'urlRegex' => $object->getUrlRegex(),
        ];
    }
}
