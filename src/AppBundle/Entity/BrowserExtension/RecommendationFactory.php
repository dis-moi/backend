<?php

namespace AppBundle\Entity\BrowserExtension;
use AppBundle\Entity;

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

    public function createFromRecommendation(Entity\Recommendation $recommendation) {

        $dto = new BrowserExtension\Recommendation();

        $dto->visibility = $recommendation->getVisibility()->getValue();
        $dto->title = $recommendation->getTitle();
        $dto->description = $recommendation->getDescription();

        if(!is_null($recommendation->getSource())) {
            $dto->source = new BrowserExtension\Source(
                "TODO",
                $recommendation->getSource()->getUrl(),
                $recommendation->getSource()->getLabel()
            );
        } else {
            $dto->source = new BrowserExtension\Source('', '', '');
        }


        $dto->contributor = [
            'image' => $this->avatarPathBuilder->__invoke($recommendation->getContributor()),
            'name' => $recommendation->getContributor()->getName()
        ];

        if(!is_null($recommendation->getContributor()->getOrganization())){
            $dto->contributor['organization'] = new BrowserExtension\Organization(
                $recommendation->getContributor()->getOrganization()->getName(),
                $recommendation->getContributor()->getOrganization()->getDescription()
            );
        } else {
            $dto->contributor['organization'] = new BrowserExtension\Organization('','');
        }

        $dto->filters = $recommendation->getCriteria()->map(function (Entity\Criterion $e) {
            return [
                'label' => $e->getLabel(),
                'description' => $e->getDescription()
            ];
        });

        $dto->alternatives = $recommendation->getAlternatives()->map(function(Entity\Alternative $e){
            return [
                'label' => $e->getLabel(),
                'url_to_redirect' => $e->getUrlToRedirect()
            ];
        });

        return $dto;
    }
}
