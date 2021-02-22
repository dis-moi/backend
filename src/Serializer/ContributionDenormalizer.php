<?php

namespace App\Serializer;

use App\DTO\Contribution;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class ContributionDenormalizer implements DenormalizerInterface
{
    public function supportsDenormalization($data, $type, $format = null): bool
    {
        return Contribution::class === $type;
    }

    public function denormalize($data, $class, $format = null, array $context = []): Contribution
    {
        return new Contribution(
            $data['url'],
            $data['contributor']['name'],
            $data['contributor']['email'],
            $data['message'],
            $data['toContributorId'] ?? null
        );
    }
}
