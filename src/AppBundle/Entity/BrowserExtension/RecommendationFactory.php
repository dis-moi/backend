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
    /**
     * @var callable
     */
    private $avatarPathBuilder;

    /**
     * RecommendationFactory constructor.
     */
    public function __construct(callable $avatarPathBuilder)
    {
        $this->avatarPathBuilder = $avatarPathBuilder;
    }

    public function createFromRecommendation(Recommendation $recommendation) {

        $dto = new BrowserExtension\Recommendation();

        $dto->visibility = $recommendation->getVisibility()->getValue();
        $dto->title = $recommendation->getTitle();
        $dto->description = $recommendation->getDescription();

        $dto->contributor = [
            'image' => $this->avatarPathBuilder->__invoke($recommendation->getContributor()),
            'name' => $recommendation->getContributor()->getName(),
            'organization' => $recommendation->getContributor()->getOrganization()
        ];

        $dto->filters = $recommendation->getFilters()->map(function(Filter $e) {
            return [
                'label' => $e->getLabel(),
                'description' => $e->getDescription()
            ];
        });

        $dto->alternatives = $recommendation->getAlternatives()->map(function(Alternative $e){
            return [
                'label' => $e->getLabel(),
                'url_to_redirect' => $e->getUrlToRedirect()
            ];
        });

        return $dto;
    }
}