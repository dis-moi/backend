<?php

declare(strict_types=1);

namespace App\Controller\Api\V3;

use App\Domain\Model\Enum\CategoryName;
use App\Repository\CategoryRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\SerializerInterface;

class GetCategoriesAction extends BaseAction
{
    /**
     * @var CategoryRepository
     */
    private $repository;

    public function __construct(SerializerInterface $serializer, CategoryRepository $repository)
    {
        parent::__construct($serializer);
        $this->repository = $repository;
    }

    /**
     * @Route("/categories")
     * @Method("GET")
     */
    public function __invoke(Request $request): JsonResponse
    {
        $categories = CategoryName::getConstants();

        if ( ! is_iterable($categories)) {
            throw new NotFoundHttpException('No categories found');
        }

        return $this->createResponse($categories);
    }
}
