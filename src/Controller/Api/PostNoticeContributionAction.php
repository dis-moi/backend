<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Contributor;
use AppBundle\Entity\NoticeContribution;
use AppBundle\Helper\NoticeContributionConverter;
use AppBundle\Repository\ContributorRepository;
use AppBundle\Repository\NoticeRepository;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Serializer\SerializerInterface;
use function AppBundle\Helper\noticeFromNoticeContribution;

class PostNoticeContributionAction extends BaseAction
{
    protected $noticeRepository;
    protected $contributorRepository;
    protected $entityManager;
    protected $noticeContributionConverter;

    public function __construct(
        SerializerInterface $serializer,
        NoticeRepository $noticeRepository,
        ContributorRepository $contributorRepository,
        EntityManagerInterface $entityManager,
        // I think we have to declare the service somewhere in a yaml or something... didn’t run the code yet :-)
        NoticeContributionConverter $noticeContributionConverter
    )
    {
        parent::__construct($serializer);
        $this->noticeRepository = $noticeRepository;
        $this->contributorRepository = $contributorRepository;
        $this->entityManager = $entityManager;
        $this->noticeContributionConverter = $noticeContributionConverter;
    }

    /**
     *
     * FIXME wite e2e test for this one and other classes we’ve created on the go
     *
     * @Route("/notices")
     * @Method("POST")
     */
    public function __invoke(Request $request)
    {
        try {

            // FIXME should we validate the data somewhere else? Have to check sanitization out...
            $noticeContribution = $this->serializer->deserialize($request->getContent(), NoticeContribution::class, 'json');
            if (!($noticeContribution instanceof NoticeContribution)) throw new InvalidArgumentException('Unable to process raw contribution data.');

            // FIXME just thinking but...
            //   would it be mad to keep converting logic (which holds business logic) into
            //   NoticeContribution entity? Like: `$noticeContribution->toContributor();`
            $contributor = $this->noticeContributionConverter::toContributor($noticeContribution);
            $this->entityManager->merge($contributor);

            $matchingContext = $this->noticeContributionConverter::toMatchingContext($noticeContribution);
            $this->entityManager->persist($matchingContext);

            $notice = $this->noticeContributionConverter::toNotice($noticeContribution)
                ->setContributor($contributor)
                ->addMatchingContext($matchingContext);
            $this->entityManager->persist($notice);
        }
        catch (\Exception $e) {
            throw new UnprocessableEntityHttpException($e->getMessage(), $e);
        }

        // FIXME should we do merge/persist here (instead doing it in earlier block)?
        //       what does flush really mean?
        $this->entityManager->flush();

        return new JsonResponse($notice, 201, [], true);
    }
}
