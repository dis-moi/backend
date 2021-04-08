<?php

declare(strict_types=1);

namespace App\Controller\Api;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;

abstract class BaseAction
{
    /**
     * @var SerializerInterface
     */
    protected $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @param mixed[]|object $content
     * @param mixed[]        $serializerOptions
     */
    protected function createResponse($content, array $serializerOptions = [], int $status = 200): JsonResponse
    {
        $headers = [];
        $json = $this->serialize($content, $serializerOptions);

        return new JsonResponse($json, $status, $headers, true);
    }

    /**
     * @param mixed   $content
     * @param mixed[] $serializerOptions
     */
    protected function serialize($content, $serializerOptions = []): string
    {
        return $this->serializer->serialize($content, 'json', $serializerOptions);
    }
}
