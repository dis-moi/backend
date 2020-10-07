<?php

namespace App\Controller\Api;

use App\Domain\Model\Enum\CategoryName;
use App\Repository\CategoryRepository;
use App\Repository\NoticeRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\SerializerInterface;

class GetCategoriesAction extends BaseAction
{
    /**
     * @var NoticeRepository
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
    public function __invoke(Request $request)
    {
        $categories = CategoryName::getConstants();

        if (!is_iterable($categories)) {
            throw new NotFoundHttpException('No categories found');
        }

        return $this->createResponse($categories);
    }
}
