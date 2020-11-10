<?php

namespace App\Controller\Api;

use App\Domain\Service\SubscriptionsTrackingService;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Serializer\SerializerInterface;

class PostSubscriptionsAction extends BaseAction
{
    /**
     * @var SubscriptionsTrackingService
     */
    private $subscriptionsTrackingService;

    public function __construct(SerializerInterface $serializer, SubscriptionsTrackingService $subscriptionsTrackingService)
    {
        parent::__construct($serializer);
        $this->subscriptionsTrackingService = $subscriptionsTrackingService;
    }

    /**
     * @Route("/subscriptions/{extensionId}")
     * @Method("POST")
     */
    public function __invoke(Request $request)
    {
        $extensionId = $request->get('extensionId', null);

        $contributorsIds = json_decode($request->getContent()) ?? [];

        try {
            $this->subscriptionsTrackingService->refreshSubscriptions($extensionId, $contributorsIds);
        } catch (Exception $e) {
            throw new UnprocessableEntityHttpException($e->getMessage(), $e);
        }

        return new JsonResponse('', 204, [], true);
    }
}
