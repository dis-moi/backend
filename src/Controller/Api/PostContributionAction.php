<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Domain\Service\EmailComposer;
use App\Domain\Service\NoticeAssembler;
use App\DTO\Contribution;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use InvalidArgumentException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Serializer\SerializerInterface;

class PostContributionAction extends BaseAction
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var NoticeAssembler
     */
    protected $noticeAssembler;

    /**
     * @var MailerInterface
     */
    private $mailer;

    /**
     * @var EmailComposer
     */
    private $emailComposer;

    public function __construct(
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager,
        NoticeAssembler $noticeAssembler,
        MailerInterface $mailer,
        EmailComposer $emailComposer
    ) {
        parent::__construct($serializer);

        $this->entityManager = $entityManager;
        $this->noticeAssembler = $noticeAssembler;
        $this->mailer = $mailer;
        $this->emailComposer = $emailComposer;
    }

    /**
     * @Route("/contributions")
     * @Method("POST")
     *
     * @throws Exception
     */
    public function __invoke(Request $request): JsonResponse
    {
        try {
            // FIXME should we validate the data somewhere else? Have to check sanitization out...
            $contribution = $this->serializer->deserialize($request->getContent(), Contribution::class, 'json');
            if (!($contribution instanceof Contribution)) {
                throw new InvalidArgumentException('Unable to process raw contribution data.');
            }

            $notice = $this->noticeAssembler->assemble($contribution);

            $this->entityManager->persist($notice);
            $this->entityManager->flush();

            $email = $this->emailComposer->composeNewContributionEmail($notice, $contribution);
            $this->mailer->send($email);

            return $this->createResponse($notice, [], 201);
        } catch (Exception $e) {
            throw new UnprocessableEntityHttpException($e->getMessage(), $e);
        } catch (TransportExceptionInterface $e) {
            throw new Exception($e->getMessage());
        }
    }
}
