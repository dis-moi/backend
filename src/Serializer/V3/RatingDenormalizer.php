<?php

declare(strict_types=1);

namespace App\Serializer\V3;

use App\Entity\Embeddable\Context;
use App\Entity\Notice;
use App\Entity\Rating;
use DateTime;
use LogicException;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;

class RatingDenormalizer implements ContextAwareDenormalizerInterface
{
    /**
     * @param mixed   $data
     * @param string  $type
     * @param string  $format
     * @param mixed[] $context
     */
    public function supportsDenormalization($data, $type, $format = null, $context = []): bool
    {
        $version = $context[NormalizerOptions::VERSION] ?? null;

        return Rating::class === $type && 3 === $version;
    }

    /**
     * @param mixed[]    $context
     * @param mixed|null $format
     * @param mixed      $data
     * @param mixed      $class
     */
    public function denormalize($data, $class, $format = null, array $context = []): Rating
    {
        $notice = $context['notice'];
        if (!$notice || !$notice instanceof Notice) {
            throw new LogicException('RatingDenormalizer->denormalize must be called with a Notice instance in the context.');
        }

        return new Rating(
            $notice,
            $data['ratingType'],
            new Context(
                new DateTime(),
                isset($data['context']['url']) ? substr($data['context']['url'], 0, Context::CONTEXT_URL_MAX_LENGTH) : '',
                $data['context']['geolocation'] ?? ''
            ),
            $data['reason'] ?? ''
        );
    }
}
