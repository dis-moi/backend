<?php
/**
 * Created by PhpStorm.
 * User: alemaire
 * Date: 18/05/2016
 * Time: 16:58
 */

namespace AppBundle\Controller;


class ApiController
{
    public function getMatchingContextsAction()
    {
        return 'matchingContext';
    }

    public function getAlternativeAction($slug)
    {
        return $slug;
    }
}