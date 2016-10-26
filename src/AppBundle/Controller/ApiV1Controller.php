<?php

namespace AppBundle\Controller;

use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\FOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class ApiV1Controller extends FOSRestController
{
    /**
     * @Route("/matchingcontexts")
     * @View()
     */
    public function getMatchingcontextsAction()
    {
        throw $this->createNotFoundException(
            'No matching contexts exists'
        );
    }
}
