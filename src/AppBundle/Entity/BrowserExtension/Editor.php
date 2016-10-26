<?php

namespace AppBundle\Entity\BrowserExtension;

class Editor
{
    /** @var string */
    public $label;

    /** @var string */
    public $url;

    /**
     * @param string $label
     * @param string $url
     */
    public function __construct($label, $url)
    {
        $this->label  = $label;
        $this->url    = $url;
    }
}
