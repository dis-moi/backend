<?php

namespace App\Domain\Service;

use App\Entity\Notice;
use Symfony\Component\Routing\RouterInterface;

class NoticeUrlGenerator
{
    protected $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function generate(Notice $notice)
    {
        return $this->router->generate('app_api_getnoticeaction__invoke', ['id' => $notice->getId()], RouterInterface::ABSOLUTE_URL);
    }
}
