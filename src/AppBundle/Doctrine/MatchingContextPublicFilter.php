<?php
namespace AppBundle\Doctrine;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;
use AppBundle\Enumerator\VisibilityEnumerator;

class MatchingContextPublicFilter extends SQLFilter
{
  public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias)
  {
       if ($targetEntity->getReflectionClass()->name != 'AppBundle\Entity\MatchingContext') {
           return '';
       }

       return sprintf('visibility = \'%s\'', (string) VisibilityEnumerator::PUBLIC_VISIBILITY());
  }
}
