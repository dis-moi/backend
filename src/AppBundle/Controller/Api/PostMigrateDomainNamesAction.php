<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\MatchingContext;
use AppBundle\Repository\DomainNameRepository;
use AppBundle\Repository\MatchingContextRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Serializer\SerializerInterface;

class PostMigrateDomainNamesAction extends BaseAction
{
    /**
     * @var MatchingContextRepository
     */
    private $matchingContextRepository;
    /**
     * @var DomainNameRepository
     */
    private $domainNameRepository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(
    SerializerInterface $serializer,
    MatchingContextRepository $matchingContextRepository,
    DomainNameRepository $domainNameRepository,
    EntityManagerInterface $entityManager
  ) {
        parent::__construct($serializer);
        $this->matchingContextRepository = $matchingContextRepository;
        $this->domainNameRepository = $domainNameRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/migrate/domains")
     * @Method("POST")
     */
    public function __invoke(Request $request)
    {
        try {
            /** @var MatchingContext $mc */
            foreach ($this->matchingContextRepository->findAll() as $mc) {
                $oldField = $mc->getDomainName();

                if ($oldField) {
                    $domainName = $this->domainNameRepository->findOrCreate($oldField);
                    if (!$mc->getDomainNames()->contains($domainName)) {
                        $mc->addDomainName($domainName);
                    }
                }
            }
            $this->entityManager->flush();
        } catch (Exception $e) {
            throw new UnprocessableEntityHttpException($e->getMessage(), $e);
        }

        return new JsonResponse('', 204, [], true);
    }
}
