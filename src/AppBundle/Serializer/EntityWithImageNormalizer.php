<?php

namespace AppBundle\Serializer;

use Symfony\Component\HttpFoundation\RequestStack;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class EntityWithImageNormalizer
{
    /**
     * @var UploaderHelper
     */
    protected $uploader;
    /**
     * @var RequestStack
     */
    protected $requestStack;

    public function __construct(UploaderHelper $uploader, RequestStack $requestStack)
    {
        $this->uploader = $uploader;
        $this->requestStack = $requestStack;
    }

    public function getImageAbsoluteUrl($entity, string $field): ?string
    {
        if ($this->uploader->asset($entity, $field)) {
            return $this->requestStack->getCurrentRequest()->getSchemeAndHttpHost().$this->uploader->asset($entity, $field);
        } else {
            return null;
        }
    }
}
