<?php
namespace AppBundle\Serializer;

use AppBundle\Helper\ContributorSubscription;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class SubscriptionDenormalizer implements DenormalizerInterface
{
    public function supportsDenormalization($data, $type, $format = null) : bool
    {
        return $type === ContributorSubscription::class;
    }

    public function denormalize($data, $class, $format = null, array $context = array()) : ContributorSubscription
    {
        return ContributorSubscription::byValue($data['ratingType']);
    }
}
