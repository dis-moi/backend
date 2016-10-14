<?php

namespace AppBundle\Entity\BrowserExtension;

class Resource
{
    /** @var string */
    public $author;

    /** @var string */
    public $url;

    /** @var string */
    public $label;

    /** @var Editor */
    public $editor;

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
