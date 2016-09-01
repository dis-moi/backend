<?php

namespace AppBundle\Entity\BrowserExtension;


use AppBundle\Entity\Contributor;
use AppBundle\Entity\Recommendation;
use AppBundle\Entity\Filter;
use AppBundle\Entity\Alternative;
use AppBundle\Entity\BrowserExtension;
use Symfony\Component\Routing\Router;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class RecommendationFactory
{
    private $vich_helper;
    private $router;

    /**
     * RecommendationFactory constructor.
     * @param Router $router
     * @param UploadHelper $vich_helper
     */
    public function __construct(Router $router, UploaderHelper $vich_helper)
    {
        $this->router = $router;
        $this->vich_helper = $vich_helper;
    }

    private function getCurrentContributorImageUrl(Contributor $contributor)
    {
        $context = $this->router->getContext();
        $port = $context->getHttpPort();
        return sprintf('%s://%s%s%s',
            $context->getScheme(),
            $context->getHost(),
            ($port == 80)? '' : ':'.$port,
            $this->vich_helper->asset($contributor, 'imageFile')
        );
    }

    private function getFlatContributor(Contributor $contributor){
        return array(
            'image' => $this->getCurrentContributorImageUrl($contributor),
            'name' => $contributor->getName(),
            'organization' => $contributor->getOrganization()
        );
    }

    private function getFlatAlternative(Alternative $alternative){
        return array(
            'label' => $alternative->getLabel(),
            'url_to_redirect' => $alternative->getUrlToRedirect()
        );
    }

    private function getFlatFilter(Filter $filter){
        return array(
            'label' => $filter->getLabel(),
            'description' => $filter->getDescription()
        );
    }

    public function createFromRecommendation(Recommendation $recommendation) {
        return new BrowserExtension\Recommendation(
            $this->getFlatContributor($recommendation->getContributor()),
            $recommendation->getVisibility()->getValue(),
            $recommendation->getTitle(),
            $recommendation->getDescription(),
            $recommendation->getAlternatives()->map(function($alternative){
                return $this->getFlatAlternative($alternative);
            }),
            $recommendation->getFilters()->map(function($filter){
                return $this->getFlatFilter($filter);
            })
        );
    }
}