<?php

namespace AppBundle\Entity\BrowserExtension;


class Recommendation
{
    private $contributor;
    private $visibility;
    private $title;
    private $description;
    private $alternatives;
    private $filters = array();

    /**
     * Recommendation constructor.
     */
    public function __construct($contributor, $visibility, $title, $description, $alternatives, $filters)
    {
        $this->contributor = $contributor;
        $this->visibility = $visibility;
        $this->title = $title;
        $this->description = $description;
        $this->alternatives = $alternatives;
        $this->filters = $filters;
    }
}