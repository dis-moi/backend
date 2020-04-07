<?php

namespace AppBundle\Serializer;

use AppBundle\Entity\Embeddable\Context;
use AppBundle\Entity\Notice;
use AppBundle\Entity\Rating;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class RatingDenormalizer implements DenormalizerInterface
{
    public function supportsDenormalization($data, $type, $format = null): bool
    {
        return Rating::class === $type;
    }

    public function denormalize($data, $class, $format = null, array $context = []): Rating
    {
        $notice = $context['notice'];
        if (!$notice || !$notice instanceof Notice) {
            throw new \LogicException('RatingDenormalizer->denormalize must be called with a Notice instance in the context.');
        }

        return new Rating(
            $notice,
            $data['ratingType'],
            new Context(
                new \DateTime(),
                isset($data['context']['url']) ? $data['context']['url'] : '',
                isset($data['context']['geolocation']) ? $data['context']['geolocation'] : ''
            ),
            isset($data['reason']) ? $data['reason'] : ''
        );
    }
}
