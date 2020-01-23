<?php


namespace AppBundle\Controller\Api;


use Domain\Service\SubscriptionsTrackingService;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Serializer\SerializerInterface;

class PostSubscriptionAction extends BaseAction
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
   * @Route("/subscriptions/{extensionUserId}/")
   * @Method("POST")
   */
  public function __invoke(Request $request)
  {
    $extensionUserId = $request->get('extensionUserId', null);

    $contributorsIds = json_decode($request->getContent());

    try
    {
      $this->subscriptionsTrackingService->refreshSubscriptions($extensionUserId, $contributorsIds);
    }
    catch (Exception $e)
    {
      throw new UnprocessableEntityHttpException($e->getMessage(), $e);
    }

    return new JsonResponse('', 204, [], true);
  }
}