<?php

namespace AppBundle\Controller\Api;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;

abstract class BaseAction
{
    protected $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    protected function createResponse($content, $status = 200, $headers = [])
    {
        $json = $this->serialize($content);

        return new JsonResponse($json, $status, $headers, true);
    }

    protected function serialize($content)
    {
        return $this->serializer->serialize($content, 'json', ['groups' => ['v3:list']]);
    }
}
