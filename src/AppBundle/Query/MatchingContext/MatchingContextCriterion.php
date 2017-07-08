<?php
/**
 * Created by PhpStorm.
 * User: alemaire
 * Date: 30/06/2017
 * Time: 16:45
 */

namespace AppBundle\Query\MatchingContext;


use Symfony\Component\HttpFoundation\Request;

class MatchingContextCriterion
{
    /**
     * @var array
     */
    public $criteria;
    /**
     * @var array
     */
    public $excludedEditors;

    /**
     * MatchingContextCriterion constructor.
     * @param array $criteria
     * @param array $excludedEditors
     */
    public function __construct(array $criteria, array $excludedEditors)
    {
        $this->criteria = $criteria;
        $this->excludedEditors = $excludedEditors;
    }

    public static function fromRequest(Request $request) : MatchingContextCriterion
    {
        $criteriaFilter = $request->get('criteria', null);
        $criteria = $criteriaFilter ? explode(",", $criteriaFilter) : [];
        $excludedEditorsFilter = $request->get('excluded_editors', null);
        $excludedEditors = $excludedEditorsFilter ? explode(",", $excludedEditorsFilter) : [];
        $excludedEditors = array_map(function($editorId) {return intval($editorId);}, $excludedEditors);
        return new self($criteria, $excludedEditors);
    }

    public function hasCriteria() : bool
    {
        return !empty($this->criteria);
    }

    public function hasExcludedEditors() : bool
    {
        return !empty($this->excludedEditors);
    }
}