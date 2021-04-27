<?php
/** @noinspection PhpUnusedAliasInspection */
/** @noinspection PhpUnused */

declare(strict_types=1);

namespace App\OpenApi;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\OpenApi;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use App\OpenApi\DocumenterInterface;


/**
 * Extends the documentation with any Services tagged as `oas_documenter`.
 * Which, in our current configuration, means implementing `DocumenterInterface`.
 *
 * Class SwaggerDecorator
 * @package App\Swagger
 */
final class OpenApiFactory implements OpenApiFactoryInterface
{
    /**
     * Usually a ApiPlatform\Core\OpenApi\Factory\OpenApiFactory
     * @var OpenApiFactoryInterface $factory
     */
    private $factory;

    /**
     * A collection of documenters, each with its own responsibility.
     * @var DocumenterInterface[]
     */
    private $documenters;

    /**
     * @var array
     */
    private $extra_data;

    public function __construct(
        OpenApiFactoryInterface $factory,
        iterable $documenters,
        array $extra
    ) {
//        $documenters_array = (array) $documenters;  // NOPE, DON'T
        $documenters_array = array();
        foreach ($documenters as $documenter) {
            $documenters_array[] = $documenter;
        }

        usort($documenters_array, function (DocumenterInterface $a, DocumenterInterface $b) {
            return $a->getOrder() - $b->getOrder();
        });

        $this->factory = $factory;
        $this->documenters = $documenters_array;
        $this->extra_data = $extra;
    }



//    public function supportsNormalization($data, $format = null)
//    {
//        print("HOYYYYYYYYYYYYYY\n");
//        die("fffffffffffffffffffffffffWHAT");
//        return $this->decorated->supportsNormalization($data, $format);
//    }
//
//    public function normalize($object, $format = null, array $context = [])
//    {
////        die("fffffffffffffffffffffffff");
////        dump($format);
////        "json"
//
////        dump($context);
////        array:2 [
////          "spec_version" => 2
////          "api_gateway" => false
////        ]
//
//        $docs = $this->decorated->normalize($object, $format, $context);
//
//        foreach ($this->documenters as $documenter) {
//            /** @var DocumenterInterface $documenter */
//            $docs = $documenter->document($docs, $object, $format, $context);
//        }
//
//        if (2 == $context['spec_version']) {
//            $docs = array_merge_recursive($docs, $this->extra_v2);
//        }
//        if (3 == $context['spec_version']) {
//            $docs = array_merge_recursive($docs, $this->extra_v3);
//        }
//
//        return $docs;
//    }

    /**
     * Creates an OpenApi class.
     * @param array $context
     * @return OpenApi
     */
    public function __invoke(array $context = []): OpenApi
    {
        $parent = $this->factory;
        $oa = $parent($context);

        foreach ($this->documenters as $documenter) {
            /** @var DocumenterInterface $documenter */
            $oa = $documenter->document($oa, $context);
        }

        return $oa;
    }
}
