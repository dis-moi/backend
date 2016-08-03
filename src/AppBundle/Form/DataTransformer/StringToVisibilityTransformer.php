<?php
namespace AppBundle\Form\DataTransformer;

use AppBundle\Enumerator\VisibilityEnumerator;
use Symfony\Component\Form\DataTransformerInterface;

class StringToVisibilityTransformer implements DataTransformerInterface
{
    /**
     * @param  string $visibilityString
     * @return VisibilityEnumerator
     * @throw InvalidArgumentException
     */
    public function transform($visibilityString)
    {
        if (null === $visibilityString) {
            return VisibilityEnumerator::PRIVATE_VISIBILITY;
        }

        return VisibilityEnumerator::get($visibilityString);
    }

    /**
     * Ensure giving to the Recommendation Entity a VisibilityEnumerator Instance
     *
     * @param  VisibilityEnumerator $visibility
     * @return VisibilityEnumerator|null
     * @throw InvalidArgumentException
     */
    public function reverseTransform($visibility)
    {
        return VisibilityEnumerator::get($visibility);
    }
}