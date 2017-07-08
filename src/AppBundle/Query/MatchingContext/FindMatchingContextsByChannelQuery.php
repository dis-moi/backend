<?php
/**
 * Created by PhpStorm.
 * User: alemaire
 * Date: 30/06/2017
 * Time: 16:38
 */

namespace AppBundle\Query\MatchingContext;


use Core\Query;

class FindMatchingContextsByChannelQuery implements Query
{
    /**
     * @var string
     */
    public $channelName;
    /**
     * @var MatchingContextCriterion
     */
    public $criterion;

    /**
     * findMatchingContextsByChannel constructor.
     * @param string $channelName
     * @param MatchingContextCriterion $criterion
     */
    public function __construct(string $channelName, MatchingContextCriterion $criterion)
    {
        $this->channelName = $channelName;
        $this->criterion = $criterion;
    }
}