<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\DTO\Contribution;
use App\Entity\Contributor;
use App\Entity\DomainName;
use App\Entity\MatchingContext;
use App\Entity\Notice;
use App\Helper\NoticeVisibility;
use App\Repository\ContributorRepository;
use App\Repository\DomainNameRepository;
use InvalidArgumentException;

class NoticeAssembler
{
    /**
     * @var ContributorRepository
     */
    private $contributorRepository;

    /**
     * @var DomainNameRepository
     */
    private $domainNameRepository;

    public function __construct(ContributorRepository $contributorRepository, DomainNameRepository $domainNameRepository)
    {
        $this->contributorRepository = $contributorRepository;
        $this->domainNameRepository = $domainNameRepository;
    }

    public function assemble(Contribution $contribution): Notice
    {
        return self::assembleNotice($contribution)
            ->setContributor(self::assembleContributor($contribution))
            ->addMatchingContext(self::assembleMatchingContext($contribution));
    }

    public function assembleContributor(Contribution $contribution): ?Contributor
    {
        if ($contribution->isAQuestion()) {
            if ($contribution->getToContributorId()) {
                /** @var Contributor|null $questionedContributor */
                $questionedContributor = $this->contributorRepository->find($contribution->getToContributorId());

                if (!$questionedContributor) {
                    throw new InvalidArgumentException("Questioned contributor doesn't exist.");
                }

                return $questionedContributor;
            }

            return null;
        }

        /** @todo /!\ Nothing here prevent a user to impersonate another one /!\ */
        /** @var Contributor|null $existingContributor */
        $existingContributor = $this->contributorRepository->findOneBy(['email' => $contribution->getContributorEmail()]);
        if ($existingContributor) {
            return $existingContributor;
        }

        return (new Contributor())
            ->setName($contribution->getContributorName())
            ->setEmail($contribution->getContributorEmail())
            ->setEnabled(true);
    }

    public function assembleMatchingContext(Contribution $contribution): MatchingContext
    {
        $url = parse_url($contribution->getUrl());
        if (!$url) {
            throw new InvalidArgumentException("Unable to parse URL `{$contribution->getUrl()}`.");
        }

        return (new MatchingContext())
            ->setUrlRegex(((array) $url)['path'] ?? '')
            ->setExampleUrl($contribution->getUrl())
            ->addDomainName($this->assembleDomainName($contribution))
            ->setDescription("Draft posted by {$contribution->getContributorName()} from the extension.");
    }

    public function assembleDomainName(Contribution $contribution): DomainName
    {
        $url = parse_url($contribution->getUrl());
        if (!$url) {
            throw new InvalidArgumentException("Unable to parse URL `{$contribution->getUrl()}`.");
        }

        $host = ((array) $url)['host'];

        if (!$host) {
            throw new InvalidArgumentException("URL has no host `{$contribution->getUrl()}`.");
        }

        $existingDomainName = $this->domainNameRepository->findMostSpecificFromHost($url['host']);
        if ($existingDomainName) {
            return $existingDomainName;
        }

        return new DomainName($host);
    }

    public static function assembleNotice(Contribution $contribution): Notice
    {
        $visibility = $contribution->isAQuestion() ? NoticeVisibility::QUESTION_VISIBILITY() : NoticeVisibility::DRAFT_VISIBILITY();
        $messagePrefix = $contribution->isAQuestion() ? "Question from {$contribution->getContributorName()}<{$contribution->getContributorEmail()}}>:" : '';

        return (new Notice())
            ->setMessage("{$messagePrefix} {$contribution->getMessage()}")
            ->setVisibility($visibility);
    }
}
