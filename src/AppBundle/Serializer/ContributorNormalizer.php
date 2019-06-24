<?php

namespace AppBundle\Serializer;

use AppBundle\Entity\Contributor;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
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
     * @var Request
     */
    protected $request;

    /**
     * @var UploaderHelper
     */
    protected $uploader;

    public function __construct(UploaderHelper $uploader, RequestStack $requestStack)
    {
        $this->uploader = $uploader;
        $this->request = $requestStack->getCurrentRequest();
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
        $url = !empty($object->getImage()) ? $this->request->getUriForPath($path) : null;

        return [
            'avatar' => $url,
            'contributions' => $object->getNoticesCount(),
            'id' => $object->getId(),
            'intro' => $object->getIntro(),
            'name' => $object->getName(),
        ];
    }
}
