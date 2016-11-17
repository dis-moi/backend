<?php

namespace AppBundle\Entity\BrowserExtension;

class Criterion
{

    /** @var string */
    public $label;

    /** @var string */
    public $slug;

    /**
     * Criterion constructor.
     *
     * @param $label
     * @param $slug
     */
    public function __construct($label, $slug)
    {
        $this->label = $label;
        $this->slug = $slug;
    }
}
