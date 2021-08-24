<?php

/** @noinspection PhpUnusedAliasInspection */
// @noinspection PhpUnused

declare(strict_types=1);

namespace App\OpenApi;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\Factory\OpenApiFactory as ApiPlatformOpenApiFactory;
use ApiPlatform\Core\OpenApi\OpenApi;

/**
 * Extends the documentation with any Services tagged as `openapi_documenter`.
 * Which, in our current configuration, means implementing `DocumenterInterface`.
 *
 * Class OpenApiFactory
 */
final class OpenApiFactory implements OpenApiFactoryInterface
{
    /**
     * Usually a ApiPlatform\Core\OpenApi\Factory\OpenApiFactory.
     *
     * @var OpenApiFactoryInterface
     */
    private $factory;

    /**
     * A collection of documenters, each with its own responsibility.
     *
     * @var DocumenterInterface[]
     */
    private $documenters;

    /**
     * OpenApiFactory constructor.
     *
     * @param OpenApiFactoryInterface       $factory     Original service we're wrapping
     * @param iterable<DocumenterInterface> $documenters List of tagged services
     */
    public function __construct(
        OpenApiFactoryInterface $factory,
        iterable $documenters
    ) {
//        $documenters_array = (array) $documenters;  // NOPE, DON'T
        $documenters_array = [];
        foreach ($documenters as $documenter) {
            $documenters_array[] = $documenter;
        }

        usort($documenters_array, function (DocumenterInterface $a, DocumenterInterface $b) {
            return $a->getOrder() - $b->getOrder();
        });

        $this->factory = $factory;
        $this->documenters = $documenters_array;
    }

    /**
     * Creates an OpenApi class.
     *
     * @param array<string, mixed> $context Context provided by ApiPlatform
     */
    public function __invoke(array $context = []): OpenApi
    {
        var_dump($context);
        $context[ApiPlatformOpenApiFactory::BASE_URL] = '/v4/';
        $parent = $this->factory;
        $oa = $parent($context);

//        $pathItem = $oa->getPaths()->getPath('/notices');
//        $operation = $pathItem->getGet();
//        $operation = $pathItem->getGet();

        foreach ($this->documenters as $documenter) {
            /** @var DocumenterInterface $documenter */
            $oa = $documenter->document($oa, $context);
        }

        return $oa;
    }
}
