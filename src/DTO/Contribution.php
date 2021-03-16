<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * A `Contribution` is a `DTO` used to create a `Notice` and `Contributor`.
 */
final class Contribution
{
    /**
     * @var string
     *
     * @Assert\Url
     */
    private $url;

    /**
     * @var string
     */
    private $contributorName;

    /**
     * @var string
     *
     * @Assert\Email
     */
    private $contributorEmail;

    /**
     * @var string
     */
    private $message;

    /**
     * This property is filled when a user is asking a question to another contributor.
     *
     * @var int|null
     */
    private $toContributorId;

    /**
     * @var bool
     */
    private $question;

    public function __construct($url, $contributorName, $contributorEmail, $message, $toContributorId = null, $question = false)
    {
        $this->url = $url;
        $this->contributorName = $contributorName;
        $this->contributorEmail = $contributorEmail;
        $this->message = $message;
        $this->toContributorId = $toContributorId;
        $this->question = $question;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getContributorName(): string
    {
        return $this->contributorName;
    }

    public function getContributorEmail(): string
    {
        return $this->contributorEmail;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getToContributorId(): ?int
    {
        return $this->toContributorId;
    }

    public function isAQuestion(): bool
    {
        return $this->question;
    }

    public function getTypeString(): string
    {
        return $this->isAQuestion() ? 'question' : 'contribution';
    }

    public function __toString(): string
    {
        return "Contribution of $this->contributorName on $this->url";
    }
}
