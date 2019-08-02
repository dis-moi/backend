<?php


namespace AppBundle\Serializer\Serializable;

class Thumb
{
    public const SMALL = 'small';
    public const NORMAL = 'normal';
    public const LARGE = 'large';

    private const SMALL_FILTER = 's_thumb';
    private const NORMAL_FILTER = 'm_thumb';
    private const LARGE_FILTER = 'l_thumb';

    private $name;
    private $filter;

    private function __construct(string $name)
    {
        $this->name = $name;
        switch ($name) {
            case self::SMALL:
                $this->filter = self::SMALL_FILTER;
                break;
            case self::NORMAL:
                $this->filter = self::NORMAL_FILTER;
                break;
            case self::LARGE:
                $this->filter = self::LARGE_FILTER;
                break;
            default:
                throw new \TypeError("Unknown thumbnail’s name '$name'; should be small, medium or large.");
        }
    }

    static function fromName(string $name) : self
    {
        return new self($name);
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function getFilter() : string
    {
        return $this->filter;
    }

}