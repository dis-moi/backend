<?php

declare(strict_types=1);

namespace App\OpenApi;

use ApiPlatform\Core\OpenApi\OpenApi;

/**
 * All services implementing this will automatically be used by the OpenApiFactory.
 * See config/services.yaml.
 *
 * Interface DocumenterInterface
 */
interface DocumenterInterface
{
    public const ORDER_VERY_FIRST = -1024;
    public const ORDER_FIRST = -32;
    public const ORDER_DEFAULT = 0;
    public const ORDER_LAST = 32;
    public const ORDER_VERY_LAST = 1024;

    public const ORDER_BEFORE = -1;
    public const ORDER_AFTER = 1;

    /**
     * Adds custom data to the $openApi and returns it, or a copy of it.
     *
     * @param OpenApi              $openApi The object as provided by ApiPlatform or previous Documenters.
     * @param array<string, mixed> $context A context provided by ApiPlatform, with not much in it.
     * @return OpenApi You may return the input $openApi after modifications, or a copy of it.
     */
    public function document(OpenApi $openApi, array $context = []): OpenApi;

    /**
     * Documenters are applied in increasing order.
     * Negative values are allowed.  The default value should be 0.
     * You may use the ORDER_XXX constants for this, if you wish.
     * When two or more documenters have the same order,
     * they are applied in the lexicographical order of their class name,
     * since that is how Symfony DIC seems to load tagged services.
     */
    public function getOrder(): int;
}
