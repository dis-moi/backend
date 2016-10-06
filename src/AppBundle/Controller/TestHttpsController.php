<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class TestHttpsController extends Controller
{
    /**
     * @Route("/test-https")
     */
    public function testAction(Request $request)
    {
        echo 'test https';die();
    }
}
