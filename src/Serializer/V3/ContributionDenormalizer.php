<?php

declare(strict_types=1);

namespace App\Serializer\V3;

use App\DTO\Contribution;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;

class ContributionDenormalizer implements ContextAwareDenormalizerInterface
{
    /**
     * @param mixed   $data
     * @param string  $type
     * @param string  $format
     * @param mixed[] $context
     */
    public function supportsDenormalization($data, $type, $format = null, array $context = []): bool
    {
        $version = $context[NormalizerOptions::VERSION] ?? null;

        return Contribution::class === $type && 3 === $version;
    }

    /**
     * @param mixed[]    $context
     * @param mixed|null $format
     * @param mixed      $data
     * @param mixed      $class
     */
    public function denormalize($data, $class, $format = null, array $context = []): Contribution
    {
        $toContributorId = isset($data['toContributorId']) ? (int) $data['toContributorId'] : null;
        $question = isset($data['question']) ? (bool) $data['question'] : (bool) $toContributorId;

        return new Contribution(
            $data['url'],
            $data['contributor']['name'],
            $data['contributor']['email'],
            $data['message'],
            $toContributorId,
            $question,
        );
    }
}
