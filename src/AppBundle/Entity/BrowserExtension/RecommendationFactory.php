<?php

namespace AppBundle\Entity\BrowserExtension;

use AppBundle\Entity\Recommendation as RecommendationEntity;
use AppBundle\Entity\Criterion as CriterionEntity;
use AppBundle\Entity\Alternative as AlternativeEntity;
use AppBundle\Entity\BrowserExtension;
use League\Uri\Components\Query;
use League\Uri\Modifiers\MergeQuery;
use League\Uri\Schemes\Http;

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

        $dto->id = $recommendation->getId();
        $dto->visibility = $recommendation->getVisibility()->getValue();
        $dto->title = $recommendation->getTitle();
        $dto->description = DataConverter::convertNewLinesToParagraphs($recommendation->getDescription());

        if (!is_null($recommendation->getResource())) {
            $dto->resource = new Resource(
                "TODO",
                self::add_utm_source($recommendation->getResource()->getUrl()),
                $recommendation->getResource()->getLabel()
            );
            if (!is_null($recommendation->getResource()->getEditor())) {
                $dto->resource->editor = new Editor(
                    $recommendation->getResource()->getEditor()->getId(),
                    $recommendation->getResource()->getEditor()->getLabel(),
                    self::add_utm_source($recommendation->getResource()->getEditor()->getUrl())
                );
            }
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
                'slug' => $e->getSlug()
            ];
        });
        $dto->filters = $dto->criteria;

        $dto->alternatives = $recommendation->getAlternatives()->map(function (AlternativeEntity $e) {
            return [
                'label' => $e->getLabel(),
                'url_to_redirect' => self::add_utm_source($e->getUrlToRedirect())
            ];
        });

        return $dto;
    }

    /**
     * @param string $url
     * @return string
     */
    static function add_utm_source(string $url): string {
        $uri = Http::createFromString($url);
        $modifier = new MergeQuery('utm_source=lmem_assistant');
        return $modifier->process($uri)->__toString();
    }
}