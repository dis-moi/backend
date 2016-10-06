<?php

namespace AppBundle\Entity\BrowserExtension;

class Resource
{

    public $author;

    public $url;

    public $label;

    /**
     * Resource constructor.
     *
     * @param $author
     * @param $url
     * @param $label
     */
    public function __construct($author, $url, $label)
    {
        $this->author = $author;
        $this->url    = $url;
        $this->label  = $label;
    }
}
