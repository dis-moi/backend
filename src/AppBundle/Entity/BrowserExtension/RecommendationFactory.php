<?php

namespace AppBundle\Entity\BrowserExtension;

use AppBundle\Entity\Recommendation as RecommendationEntity;
use AppBundle\Entity\Criterion as CriterionEntity;
use AppBundle\Entity\Alternative as AlternativeEntity;
use AppBundle\Entity\BrowserExtension;
use Symfony\Component\Routing\Router;

class RecommendationFactory
{
    /**
     * @var callable
     */
    private $avatarPathBuilder;

    /**
     * RecommendationFactory constructor.
     *
     * @param callable $avatarPathBuilder
     */
    public function __construct(callable $avatarPathBuilder)
    {
        $this->avatarPathBuilder = $avatarPathBuilder;
    }

    /**
     * @param RecommendationEntity $recommendation
     *
     * @return Recommendation
     */
    public function createFromRecommendation(RecommendationEntity $recommendation)
    {

        $dto = new BrowserExtension\Recommendation();

        $dto->visibility = $recommendation->getVisibility()->getValue();
        $dto->title = $recommendation->getTitle();
        $dto->description = $recommendation->getDescription();

        if (!is_null($recommendation->getResource())) {
            $dto->resource = new Resource(
                "TODO",
                $recommendation->getResource()->getUrl(),
                $recommendation->getResource()->getLabel()
            );
        } else {
            $dto->resource = new Resource('', '', '');
        }
        /* for compatibility while the extension is being updated */
        $dto->source = $dto->resource;


        $dto->contributor = [
            'image' => $this->avatarPathBuilder->__invoke($recommendation->getContributor()),
            'name' => $recommendation->getContributor()->getName(),
            'organization' => $recommendation->getContributor()->getOrganization()
        ];

        $dto->criteria = $recommendation->getCriteria()->map(function (CriterionEntity $e) {
            return [
                'label' => $e->getLabel(),
                'description' => $e->getDescription()
            ];
        });
        $dto->filters = $dto->criteria;

        $dto->alternatives = $recommendation->getAlternatives()->map(function (AlternativeEntity $e) {
            return [
                'label' => $e->getLabel(),
                'url_to_redirect' => $e->getUrlToRedirect()
            ];
        });

        return $dto;
    }
}