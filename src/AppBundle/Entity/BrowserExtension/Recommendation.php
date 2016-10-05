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

class Source
{
    public $author;
    public $url;
    public $label;

    /**
     * Resource constructor.
     * @param $author
     * @param $url
     * @param $label
     */
    public function __construct($author, $url, $label)
    {
        $this->author = $author;
        $this->url = $url;
        $this->label = $label;
    }
}