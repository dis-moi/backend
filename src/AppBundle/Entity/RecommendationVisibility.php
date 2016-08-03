<?php

namespace AppBundle\Entity;
use MabeEnum\Enum;

class RecommendationVisibility extends Enum {
    const PUBLIC_VISIBILITY = 'public';
    const PRIVATE_VISIBILITY = 'private';
}