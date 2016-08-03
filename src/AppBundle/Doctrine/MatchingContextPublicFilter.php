<?php
namespace AppBundle\Doctrine;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;
use AppBundle\Entity\RecommendationVisibility;

class MatchingContextPublicFilter extends SQLFilter
{
  public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias)
  {
       if ($targetEntity->getReflectionClass()->getName() != 'AppBundle\Entity\MatchingContext') {
           return '';
       }

       return sprintf('visibility = \'%s\'', RecommendationVisibility::PUBLIC_VISIBILITY()->getValue());
  }
}
