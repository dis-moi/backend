<?php


namespace AppBundle\Serializer;


use AppBundle\Entity\NoticeContribution;
use AppBundle\Helper\NoticeIntention;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class NoticeContributionDenormalizer implements DenormalizerInterface
{
    public function supportsDenormalization($data, $type, $format = null) : bool
    {
        return $type === NoticeContribution::class;
    }

    public function denormalize($data, $class, $format = null, array $context = array()) : NoticeContribution
    {
        $url = $data['url'];
        $intention = NoticeIntention::get($data['intention']);
        $contributorName = $data['contributorName'];
        $contributorEmail = $data['contributorEmail'];
        $message = $data['message'];

        return new NoticeContribution($contributorName, $contributorEmail, $url, $intention, $message);
    }
}