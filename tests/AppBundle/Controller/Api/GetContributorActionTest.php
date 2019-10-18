<?php

namespace Tests\AppBundle\Controller\Api;

use AppBundle\Controller\Api\GetContributorAction;
use AppBundle\Repository\ContributorRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;

class GetContributorActionTest extends TestCase
{
    public function test__invoke()
    {
        $expectedResult = new JsonResponse('json', 200, [], true);

        $serializer = $this->getMockBuilder(SerializerInterface::class)
            ->getMockForAbstractClass();
        $repository = $this->getMockBuilder(ContributorRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $request->expects($this->once())->method('get')
            ->with('id')->willReturn(42);

        $repository->expects($this->once())->method('getOne')
            ->with(42)
            ->willReturn('contributor');

        $serializer->expects($this->once())->method('serialize')
            ->with('contributor')
            ->willReturn('json');

        $action = new GetContributorAction($serializer, $repository);

        $this->assertEquals($expectedResult->getContent(), $action($request)->getContent());
    }
}
