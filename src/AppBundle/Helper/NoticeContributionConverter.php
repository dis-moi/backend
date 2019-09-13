<?php

namespace AppBundle\Helper;

use AppBundle\Entity\Contributor;
use AppBundle\Entity\MatchingContext;
use AppBundle\Entity\Notice;
use AppBundle\Entity\NoticeContribution;
use InvalidArgumentException;

class NoticeContributionConverter
{

    static function toContributor(NoticeContribution $contribution): Contributor
    {
        $contributor = new Contributor();
        $contributor
            ->setName($contribution->getContributorName())
            ->setEmail($contribution->getContributorEmail());
        return $contributor;
    }

    /**
     * @throws InvalidArgumentException
     */
    static function toMatchingContext(NoticeContribution $contribution): MatchingContext
    {
        $url = parse_url($contribution->getUrl());
        if (!$url) throw new InvalidArgumentException("Unable to parse URL “{$contribution->getUrl()}”");

        $domainName = $url['host'];
        $urlRegex = $url['path'] . '(?.+)?(#.+)?$'; // FIXME check out optional query and fragment (not sure this regex works well)

        return (new MatchingContext())
            ->setDomainName($domainName)
            ->setUrlRegex($urlRegex);
    }

    static function toNotice(NoticeContribution $contribution): Notice
    {
        return (new Notice())
            ->setIntention($contribution->getIntention())
            ->setMessage($contribution->getMessage())
            ->setVisibility(NoticeVisibility::PRIVATE_VISIBILITY());
    }

}
