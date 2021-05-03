<?php

declare(strict_types=1);

namespace App\Serializer\V3;

use App\Serializer\Serializable\Picture;
use App\Serializer\Serializable\Thumb;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class PictureNormalizer implements ContextAwareNormalizerInterface, NormalizerAwareInterface
{
    /**
     * @var NormalizerInterface
     */
    protected $normalizer;

    /**
     * @var UploaderHelper
     */
    protected $uploader;

    /**
     * @var CacheManager
     */
    protected $cacheManager;

    public function __construct(UploaderHelper $uploader, CacheManager $cacheManager)
    {
        $this->uploader = $uploader;
        $this->cacheManager = $cacheManager;
    }

    public function setNormalizer(NormalizerInterface $normalizer): void
    {
        $this->normalizer = $normalizer;
    }

    /**
     * @param mixed   $data
     * @param string  $format
     * @param mixed[] $context
     */
    public function supportsNormalization($data, $format = null, $context = []): bool
    {
        $version = $context[NormalizerOptions::VERSION] ?? null;

        return $data instanceof Picture && 3 === $version;
    }

    /**
     * @param string  $format
     * @param mixed[] $context
     * @param mixed   $object
     *
     * @return mixed[]
     */
    public function normalize($object, $format = null, array $context = []): array
    {
        if ( ! ($object instanceof Picture)) {
            throw new InvalidArgumentException();
        }
        $path = $this->uploader->asset($object->getUploadable(), 'imageFile');

        return array_map(
            function (Thumb $thumb) use ($path) {
                return [
                    'url' => $this->cacheManager->getBrowserPath($path, $thumb->getFilter()),
                ];
            },
            $object->getThumbs()
        );
    }
}
