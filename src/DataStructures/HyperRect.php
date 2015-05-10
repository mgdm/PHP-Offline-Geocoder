<?php

namespace MGDM\DataStructures;

class HyperRect {

    /** @var array */
    private $min;

    /** @var array */
    private $max;

    function __construct($min, $max)
    {
        $this->min = $min;
        $this->max = $max;
    }

    /**
     * @return array
     */
    public function getMin()
    {
        return $this->min;
    }

    /**
     * @param array $min
     */
    public function setMin($min)
    {
        $this->min = $min;
    }

    /**
     * @return array
     */
    public function getMax()
    {
        return $this->max;
    }

    /**
     * @param array $max
     */
    public function setMax($max)
    {
        $this->max = $max;
    }
}