<?php

namespace App\Domain\Service;

use App\DTO\Contribution;
use App\Entity\Notice;
use EasyCorp\Bundle\EasyAdminBundle\Router\EasyAdminRouter;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

class EmailComposer
{
    private $easyAdminRouter;
    private $instanceName;
    private $noreplyEmail;
    private $systemEmail;

    public function construct(EasyAdminRouter $easyAdminRouter, string $instanceName, string $systemEmail, string $noreplyEmail)
    {
        $this->easyAdminRouter = $easyAdminRouter;
        $this->instanceName = $instanceName;
        $this->noreplyEmail = $noreplyEmail;
        $this->systemEmail = $systemEmail;
    }

    public function composeNewContributionEmail(Notice $notice, Contribution $contribution): TemplatedEmail
    {
        $subject = $this->composeNewContributionSubject($notice, $contribution);

        return (new TemplatedEmail())
            ->from($this->noreplyEmail)
            ->to($this->systemEmail)
            ->replyTo($contribution->getContributorEmail())
            ->subject($subject)
            ->htmlTemplate('emails/new_contribution.html.twig')
            ->context([
                'notice' => $notice,
                'noticeURL' => $this->easyAdminRouter->getEntityPath($notice, 'edit'),
            ]);
    }

    private function composeNewContributionSubject(Notice $notice, Contribution $contribution): string
    {
        $subject = "[{$this->instanceName}]";
        if ($contribution->isAQuestion()) {
            $subject .= "[{$this->instanceName}] New question";
            if ($contribution->getToContributorId()) {
                $subject .= " to {$notice->getContributor()->getName()}";
            }
        } else {
            $subject .= "[{$this->instanceName}] New contribution from {$contribution->getContributorName()}";
        }

        return $subject;
    }
}
