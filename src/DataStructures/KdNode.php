<?php

namespace MGDM\DataStructures;

/**
 * Class KdNode
 * @package MGDM\DataStructures
 */
class KdNode {

    /** @var array */
    private $point;

    /** @var int */
    private $dimension;

    /** @var KdNode */
    private $left;

    /** @var KdNode */
    private $right;

    /** @var mixed */
    private $value;

    /** @var int */
    private $split = 0;

    /**
     * @param $point
     * @param null $value
     */
    public function __construct($point, $value = null)
    {
        $this->point = $point;
        $this->value = $value;
        $this->dimension = count($point);
    }

    /**
     * @param KdNode $node
     * @return int
     */
    public function sqd(KdNode $node)
    {
        $point = $node->getPoint();
        if (count($point) != $this->dimension) {
            throw new \InvalidArgumentException("Dimension of node does not match");
        }

        $sum = 0;
        foreach ($this->getPoint() as $dimension => $coordinate) {
            $d = $coordinate - $point[$dimension];
            $sum += $d * $d;
        }

        return $sum;
    }

    /**
     * @return array
     */
    public function getPoint()
    {
        return $this->point;
    }

    /**
     * @return KdNode
     */
    public function getLeft()
    {
        return $this->left;
    }

    /**
     * @param KdNode $left
     */
    public function setLeft($left)
    {
        $this->left = $left;
    }

    /**
     * @return KdNode
     */
    public function getRight()
    {
        return $this->right;
    }

    /**
     * @param KdNode $right
     */
    public function setRight($right)
    {
        $this->right = $right;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return int
     */
    public function getSplit()
    {
        return $this->split;
    }

    /**
     * @param int $split
     */
    public function setSplit($split)
    {
        $this->split = $split;
    }

}