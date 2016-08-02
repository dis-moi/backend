<?php
namespace AppBundle\Doctrine;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;

class MatchingContextPrivateFilter extends SQLFilter
{
  public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias)
  {
       if ($targetEntity->getReflectionClass()->name != 'AppBundle\Entity\MatchingContext') {
           return '';
       }

       return 'visibility = \'private\'';
  }
}
