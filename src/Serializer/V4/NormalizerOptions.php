<?php

declare(strict_types=1);


namespace App\Serializer\V4;


use App\Serializer\V3\NormalizerOptions as NormalizerOptionsV3;


final class NormalizerOptions
{
    public const VERSION = NormalizerOptionsV3::VERSION;
    public const SKIP_NOTICE = 'skip_notice';
}
