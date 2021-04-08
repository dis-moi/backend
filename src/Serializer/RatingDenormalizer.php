<?php

declare(strict_types=1);

namespace App\Serializer;

use App\Entity\Embeddable\Context;
use App\Entity\Notice;
use App\Entity\Rating;
use DateTime;
use LogicException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class RatingDenormalizer implements DenormalizerInterface
{
    public function supportsDenormalization($data, $type, $format = null): bool
    {
        return Rating::class === $type;
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
