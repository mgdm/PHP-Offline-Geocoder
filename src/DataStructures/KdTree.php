<?php

namespace MGDM\DataStructures;

/**
 * Class KdTree
 * @package MGDM\DataStructures
 */
/**
 * Class KdTree
 * @package MGDM\DataStructures
 */
class KdTree {

    /** @var KdNode */
    private $root;

    /** @var HyperRect */
    private $bounds;

    /** @var int */
    private $dimension;

    /** @var int */
    private $split;


    /**
     * @param HyperRect $bounds
     * @param int $dimension
     * @param array $nodes
     */
    public function __construct(HyperRect $bounds, $dimension, $nodes = [])
    {
        $this->bounds = $bounds;
        $this->dimension = $dimension;

        array_walk($nodes, [$this, 'validateNode']);

        $this->root = $this->buildTree(0, $nodes);
    }

    private function validateNode($node)
    {
        if (!$node instanceof KdNode) {
            throw new \InvalidArgumentException("Input types must be instances of " . __NAMESPACE__ . "\\KdNode");
        }

        $node->setSplit(0);
    }

    /**
     * @param int $split
     * @param array $nodes
     * @return KdNode|null
     */
    protected function buildTree($split, array $nodes)
    {
        if (count($nodes) === 0) {
            return null;
        }

        if (count($nodes) === 1) {
            $node = $nodes[0];
            return $nodes[0];
        }

        $nodes = $this->sortByDimension($nodes, $split);

        $medianIndex = $this->findMedian($nodes, $split);
        $leftNodes = array_slice($nodes, 0, $medianIndex);
        $rightNodes = array_slice($nodes, $medianIndex + 1);

        /** @var KdNode $medianNode */
        $medianNode = $nodes[$medianIndex];
        $newSplit = ($split + 1) % count($medianNode->getPoint());
        $medianNode->setLeft($this->buildTree($newSplit, $leftNodes));
        $medianNode->setRight($this->buildTree($newSplit, $rightNodes));
        return $medianNode;
    }

    protected function sortByDimension($nodes, $dimension)
    {
        $vals = array_map(function($n) use ($dimension) {
            return $n->getPoint()[$dimension];
        }, $nodes);

        asort($vals);
        $sortedNodes = array_map(function($v) use ($nodes) {
            return $nodes[$v];
        }, array_keys($vals));

        return $sortedNodes;
    }

    /**
     * @param KdNode $target
     * @return array
     */
    public function nearestNeighbour(KdNode $target)
    {
        $split = $this->split;
        $pivot = $this->root;

        return $this->nn($this->root, $target, $this->bounds, PHP_INT_MAX);
    }

    /**
     * @param KdNode $node
     * @param KdNode $target
     * @param HyperRect $hr
     * @param float $maxDistSqd
     * @return array
     */
    private function nn($node, KdNode $target, HyperRect $hr, $maxDistSqd)
    {
        if ($node === null) {
            $newPoint = array_fill(0, $this->dimension, 0.0);
            return [new KdNode($newPoint), PHP_INT_MAX];
        }

        $pivot = $node->getPoint();
        $split = $node->getSplit();
        $targetPoint = $target->getPoint();

        $leftHr = clone($hr);
        $rightHr = clone($hr);

        $leftHrMax = $leftHr->getMax();
        $leftHrMax[$split] = $pivot[$split];
        $leftHr->setMax($leftHrMax);

        $rightHrMin = $rightHr->getMin();
        $rightHrMin[$split] = $pivot[$split];
        $rightHr->setMin($rightHrMin);

        if ($targetPoint[$split] <= $pivot[$split]) {
            $nearerKd = $node->getLeft();
            $nearerHr = $leftHr;
            $furtherKd = $node->getRight();
            $furtherHr = $rightHr;
        } else {
            $nearerKd = $node->getRight();
            $nearerHr = $rightHr;
            $furtherKd = $node->getLeft();
            $furtherHr = $leftHr;
        }

        list($nearest, $distSqd) = $this->nn($nearerKd, $target, $nearerHr, $maxDistSqd);

        if ($distSqd < $maxDistSqd) {
            $maxDistSqd = $distSqd;
        }

        $d = pow($pivot[$split] - $targetPoint[$split], 2);

        if ($d > $maxDistSqd) {
            return [$nearest, $distSqd];
        }

        $d = $node->sqd($target);
        if ($d < $distSqd) {
            $nearest = $node;
            $distSqd = $d;
            $maxDistSqd = $distSqd;
        }

        $n2 = $this->nn($furtherKd, $target, $furtherHr, $maxDistSqd);
        if ($n2[1] < $distSqd) {
            $nearest = $n2[0];
            $distSqd = $n2[1];
        }

        return [$nearest, $distSqd];
    }

    /**
     * @param array $nodes
     * @param int $dimension
     * @return int
     */
    protected function findMedian(array $nodes, $dimension)
    {
        $nodeCount = count($nodes);
        $medianIndex = (int) floor($nodeCount / 2);

        for ($i = $medianIndex; $i < $nodeCount - 1; $i++) {
            $p = $nodes[$i]->getPoint()[$dimension];
            if ($nodes[$i + 1]->getPoint()[$dimension] === $p) {
                $medianIndex++;
            }
        }

        return $medianIndex;
    }

    /**
     * @return int
     */
    public function getDimension()
    {
        return $this->dimension;
    }
}