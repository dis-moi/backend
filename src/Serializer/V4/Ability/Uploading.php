<?php

declare(strict_types=1);

namespace App\Serializer\V4\Ability;


use Symfony\Component\HttpFoundation\RequestStack;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;


trait Uploading
{
    /**
     * @var UploaderHelper
     */
    protected $uploader;

    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * @required
     * @param UploaderHelper $uploader Service injected by the DIC.
     */
    public function setUploader(UploaderHelper $uploader): void
    {
        $this->uploader = $uploader;
    }

    /**
     * @required
     * @param RequestStack $requestStack Service injected by the DIC.
     */
    public function setRequestStack(RequestStack $requestStack): void
    {
        $this->requestStack = $requestStack;
    }

    public function getImageAbsoluteUrl(object $entity, string $field): ?string
    {
        return $this->getAssetAbsoluteUrl($entity, $field);
    }

    public function getAssetAbsoluteUrl(object $entity, string $field): ?string
    {
        $assetPath = $this->uploader->asset($entity, $field);
        if ($assetPath) {
            return
                $this->requestStack->getCurrentRequest()->getSchemeAndHttpHost()
                .
                $assetPath;
        }

        return null;
    }

}