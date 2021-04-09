<?php

declare(strict_types=1);

namespace App\Tests\Controller\Api\V3;

use App\Controller\Api\V3\GetNoticeAction;
use App\Entity\Notice;
use App\Repository\NoticeRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

class GetNoticeActionTest extends TestCase
{
    public function testInvoke(): void
    {
        $expectedResult = new JsonResponse('json', 200, [], true);

        $serializer = $this->getMockBuilder(SerializerInterface::class)
            ->getMockForAbstractClass();
        $repository = $this->getMockBuilder(NoticeRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $notice = new Notice();

        $request->expects($this->once())->method('get')
            ->with('id')->willReturn(33);
        $repository->expects($this->once())->method('getOne')
            ->with(33)
            ->willReturn($notice);
        $serializer->expects($this->once())->method('serialize')
            ->with($notice)
            ->willReturn('json');

        $action = new GetNoticeAction($serializer, $repository);

        $this->assertEquals($expectedResult->getContent(), $action($request)->getContent());
    }
}
