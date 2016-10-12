<?php

namespace AppBundle\Entity\BrowserExtension;


class Recommendation
{
    public $contributor;
    public $visibility;
    public $title;
    public $description;
    public $alternatives;
    public $source;
    public $resource;
    public $criteria = array();
    public $filters = array();
}
