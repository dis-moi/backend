<?php

declare(strict_types=1);

namespace App\Tests\Controller\Api\V3;

use App\Controller\Api\V3\GetMatchingContextsAction;
use App\Repository\MatchingContextRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

class GetMatchingContextsActionTest extends TestCase
{
    /**
     * @return mixed[]
     */
    public function invokeData(): array
    {
        return [
            [null],
            [[1, 2]],
        ];
    }

    /**
     * @dataProvider invokeData
     *
     * @param mixed[] $contributors
     */
    public function testInvoke(?array $contributors): void
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
