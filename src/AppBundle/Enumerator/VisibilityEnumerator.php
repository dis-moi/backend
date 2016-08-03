<?php

namespace AppBundle\Enumerator;
use MabeEnum\Enum;

class VisibilityEnumerator extends Enum {
    const PUBLIC_VISIBILITY = 'public';
    const PRIVATE_VISIBILITY = 'private';
}