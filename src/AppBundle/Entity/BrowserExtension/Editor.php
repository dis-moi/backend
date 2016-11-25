<?php

namespace AppBundle\Entity\BrowserExtension;

class Editor
{

    /** @var int */
    public $id;

    /** @var string */
    public $label;

    /** @var string */
    public $url;

    /**
     * @param int    $id
     * @param string $label
     * @param string $url
     */
    public function __construct($id, $label, $url)
    {
        $this->id    = $id;
        $this->label = $label;
        $this->url   = $url;
    }
}
