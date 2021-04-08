<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\DTO\Contribution;
use App\Entity\Notice;
use ReflectionClass;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class EmailComposer
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var string
     */
    private $instanceName;

    /**
     * @var string
     */
    private $instanceEmail;

    public function __construct(RouterInterface $router, string $instanceName, string $instanceEmail)
    {
        $this->router = $router;
        $this->instanceName = $instanceName;
        $this->instanceEmail = $instanceEmail;
    }

    public function composeNewContributionEmail(Notice $notice, Contribution $contribution): TemplatedEmail
    {
        $subject = $this->composeNewContributionSubject($notice, $contribution);

        return (new TemplatedEmail())
            ->from($contribution->getContributorEmail())
            ->to($this->instanceEmail)
            ->replyTo($contribution->getContributorEmail())
            ->subject($subject)
            ->htmlTemplate('emails/new_contribution.html.twig')
            ->context([
                'notice' => $notice,
                'noticeURL' => $this->router->generate(
                    'easyadmin',
                    [
                        'entity' => (new ReflectionClass($notice))->getShortName(),
                        'id' => $notice->getId(),
                        'action' => 'edit',
                    ],
                    UrlGeneratorInterface::ABSOLUTE_URL
                ),
            ]);
    }

    private function composeNewContributionSubject(Notice $notice, Contribution $contribution): string
    {
        $subject = "[{$this->instanceName}]";
        if ($contribution->isAQuestion()) {
            $subject .= ' New question';
            if ($contribution->getToContributorId()) {
                $subject .= " to {$notice->getContributor()->getName()}";
            }
        } else {
            $subject .= " New contribution from {$contribution->getContributorName()}";
        }

        return $subject;
    }
}
