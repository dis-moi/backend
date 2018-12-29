<?php

namespace Tests\AppBundle\Controller\Api;

use AppBundle\Controller\Api\GetMatchingContextsAction;
use AppBundle\Repository\MatchingContextRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

class GetMatchingContextsActionTest extends TestCase
{
    public function invokeData()
    {
        return [
            [null],
            [[1,2]]
        ];
    }

    /**
     * @dataProvider invokeData
     */
    public function test__invoke($contributors)
    {
        $expectedResult = new JsonResponse('json', 200, [], true);

        $serializer = $this->getMockBuilder(SerializerInterface::class)
            ->getMockForAbstractClass();
        $repository = $this->getMockBuilder(MatchingContextRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $request->expects($this->once())->method('get')
            ->with('contributors')->willReturn($contributors);
        $repository->expects($this->once())->method('findAllPublicMatchingContext')
            ->with($contributors)->willReturn(['matchingContexts']);
        $serializer->expects($this->once())->method('serialize')
            ->with(['matchingContexts'])
            ->willReturn('json');

        $action = new GetMatchingContextsAction($serializer, $repository);

        $this->assertEquals($expectedResult->getContent(), $action($request)->getContent());
    }
}
