<?php

namespace Tests\AppBundle\Controller\Api;

use AppBundle\Controller\Api\PostContributorRatingAction;
use AppBundle\Entity\Contributor;
use AppBundle\Helper\ContributorSubscription;
use AppBundle\Repository\ContributorRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

class PostContributorRatingActionTest extends TestCase
{
    public function test__invoke()
    {
        $contributor = $this->getMockBuilder(Contributor::class)
            ->getMock();

        $serializer = $this->getMockBuilder(SerializerInterface::class)
            ->getMockForAbstractClass();
        $contributorRepository = $this->getMockBuilder(ContributorRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $entityManager = $this->getMockBuilder(EntityManagerInterface::class)
            ->getMock();
        $request = $this->getMockBuilder(Request::class)
            ->getMock();

        $request->expects($this->once())->method('getContent')
            ->willReturn('foo');
        $request->expects($this->once())->method('get')
            ->with('id')->willReturn(42);

        $contributorRepository->expects($this->once())->method('getOne')
            ->with(42)
            ->willReturn($contributor);

        $serializer->expects($this->once())->method('deserialize')
            ->with('foo', ContributorSubscription::class, 'json')
            ->willReturn(ContributorSubscription::SUBSCRIBE());

        $action = new PostContributorRatingAction($serializer, $contributorRepository, $entityManager);

        $response = $action($request);
        $this->assertEquals('', $response->getContent());
        $this->assertEquals(204, $response->getStatusCode());
    }
}
