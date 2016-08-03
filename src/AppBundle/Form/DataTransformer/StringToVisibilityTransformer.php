<?php
namespace AppBundle\Form\DataTransformer;

use AppBundle\Entity\RecommendationVisibility;
use Symfony\Component\Form\DataTransformerInterface;

class StringToVisibilityTransformer implements DataTransformerInterface
{
    /**
     * @param  string $visibilityString
     * @return RecommendationVisibility
     * @throw InvalidArgumentException
     */
    public function transform($visibilityString)
    {
        if (null === $visibilityString) {
            return RecommendationVisibility::getDefault();
        }

        return RecommendationVisibility::get($visibilityString);
    }

    /**
     * Ensure giving to the Recommendation Entity a RecommendationVisibility Instance
     *
     * @param  RecommendationVisibility $visibility
     * @return RecommendationVisibility|null
     * @throw InvalidArgumentException
     */
    public function reverseTransform($visibility)
    {
        return RecommendationVisibility::get($visibility);
    }
}