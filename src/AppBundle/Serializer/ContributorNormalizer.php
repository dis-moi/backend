<?php

namespace AppBundle\Serializer;

use AppBundle\Entity\Contributor;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class ContributorNormalizer implements NormalizerInterface, NormalizerAwareInterface
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

    /**
     * Sets the owning Normalizer object.
     *
     */
    public function setNormalizer(NormalizerInterface $normalizer)
    {
        $this->normalizer = $normalizer;
    }

    public function supportsNormalization($data, $format = null) : bool
    {
        return $data instanceof Contributor;
    }

    public function normalize($object, $format = null, array $context = array()) : array
    {
        if (!($object instanceof Contributor)) throw new InvalidArgumentException();

        $path = $this->uploader->asset($object, 'imageFile');

        return [
            'avatar' => !empty($object->getImage()) ? [
                'small' => $this->cacheManager->getBrowserPath($path, 's_thumb'),
                'normal' => $this->cacheManager->getBrowserPath($path, 'm_thumb'),
                'large' => $this->cacheManager->getBrowserPath($path, 'l_thumb'),
            ] : null,
            'contributions' => $object->getNoticesCount(),
            'id' => $object->getId(),
            'intro' => $object->getIntro(),
            'name' => $object->getName(),
        ];
    }
}
