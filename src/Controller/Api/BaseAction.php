<?php

namespace App\Controller\Api;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;

abstract class BaseAction
{
    protected $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    protected function createResponse($content, $serializerOptions = [], $status = 200)
    {
        $headers = [];
        $json = $this->serialize($content, $serializerOptions);

        return new JsonResponse($json, $status, $headers, true);
    }

    protected function serialize($content, $serializerOptions = [])
    {
        return $this->serializer->serialize($content, 'json', $serializerOptions);
    }
}
