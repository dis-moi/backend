<?php

namespace Tests\AppBundle\Controller\Api;

use AppBundle\Controller\Api\GetContributorsAction;
use AppBundle\Repository\ContributorRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;

class GetContributorsActionTest extends TestCase
{
    public function test__invoke()
    {
        $expectedResult = new JsonResponse('json', 200, [], true);

        $serializer = $this->getMockBuilder(SerializerInterface::class)
            ->getMockForAbstractClass();
        $repository = $this->getMockBuilder(ContributorRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $repository->expects($this->once())->method('getAllEnabledWithAtLeastOneContribution')
            ->willReturn(['contributors']);
        $serializer->expects($this->once())->method('serialize')
            ->with(['contributors'])
            ->willReturn('json');

        $action = new GetContributorsAction($serializer, $repository);

        $this->assertEquals($expectedResult->getContent(), $action()->getContent());
    }
}
