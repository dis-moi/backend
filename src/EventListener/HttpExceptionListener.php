<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: insitu
 * Date: 21/02/19
 * Time: 18:01.
 */

namespace App\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class HttpExceptionListener
{
    public function onKernelException(GetResponseForExceptionEvent $event): void
    {
        $exception = $event->getException();

        if ($this->isJsonRequest($event->getRequest()) && $exception instanceof HttpExceptionInterface) {
            $error = json_encode([
                'error' => $exception->getMessage(),
            ]);
            $response = new JsonResponse($error, $exception->getStatusCode(), $exception->getHeaders(), true);
            $event->setResponse($response);
        }
    }

    private function isJsonRequest(Request $request): bool
    {
        return 'json' === $request->getContentType();
    }
}
