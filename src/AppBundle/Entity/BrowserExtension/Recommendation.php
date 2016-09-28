<?php

namespace AppBundle\Entity\BrowserExtension;


class Recommendation
{
    public $contributor;
    public $visibility;
    public $title;
    public $description;
    public $alternatives;
    public $filters = array();
}