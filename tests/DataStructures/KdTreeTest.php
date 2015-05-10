<?php
namespace MGDM\Tests\DataStructures;

use MGDM\DataStructures\HyperRect;
use MGDM\DataStructures\KdNode;
use MGDM\DataStructures\KdTree;

class KdTreeTest extends \PHPUnit_Framework_TestCase {

    private $wikipediaData = [
        [2, 3], [5, 4], [9, 6], [4, 7], [8, 1], [7, 2]
    ];

    private $threeDimensions = [
        [76, 60, 40],
        [76, 5, 43],
        [6, 38, 45],
        [42, 69, 78],
        [48, 91, 79],
        [25, 69, 68],
        [79, 94, 46],
        [22, 12, 84],
        [15, 24, 2],
        [45, 93, 34],
        [0, 80, 82],
        [89, 40, 20],
        [72, 91, 28],
        [72, 86, 1],
        [31, 89, 27],
        [44, 86, 21],
        [88, 64, 33],
        [95, 99, 81],
        [38, 17, 1],
        [55, 24, 64],
        [65, 34, 28],
        [31, 75, 36],
        [57, 37, 54],
        [83, 46, 79],
        [54, 91, 47],
        [61, 81, 29],
        [57, 29, 4],
        [10, 91, 89],
        [100, 95, 40],
        [14, 92, 46],
        [42, 34, 14],
        [36, 35, 56],
        [32, 64, 18],
        [45, 87, 34],
        [20, 47, 64],
        [52, 37, 84],
        [25, 58, 24],
        [31, 43, 11],
        [64, 40, 70],
        [34, 80, 71],
        [23, 9, 20],
        [58, 97, 94],
        [50, 36, 60],
        [24, 90, 38],
        [94, 54, 16],
        [100, 76, 72],
        [41, 97, 80],
        [48, 76, 49],
        [41, 90, 75],
        [39, 42, 53],
        [5, 4, 33],
        [43, 40, 79],
        [23, 23, 60],
        [44, 32, 46],
        [28, 35, 99],
        [91, 4, 80],
        [48, 62, 39],
        [13, 87, 64],
        [56, 3, 2],
        [8, 94, 55],
        [49, 28, 16],
        [61, 28, 98],
        [18, 48, 33],
        [14, 17, 65],
        [58, 38, 58],
        [43, 33, 39],
        [52, 1, 28],
        [74, 30, 7],
        [6, 35, 24],
        [39, 49, 43],
        [57, 42, 77],
        [74, 65, 90],
        [55, 80, 8],
        [89, 58, 18],
        [80, 83, 31],
        [34, 82, 75],
        [21, 69, 27],
        [68, 54, 97],
        [90, 46, 47],
        [28, 54, 6],
        [54, 94, 66],
        [19, 14, 58],
        [32, 22, 62],
        [38, 17, 55],
        [8, 0, 54],
        [87, 0, 15],
        [28, 60, 34],
        [84, 64, 73],
        [39, 60, 1],
        [59, 93, 26],
        [76, 2, 0],
        [63, 33, 44],
        [70, 1, 79],
        [48, 74, 39],
        [22, 34, 95],
        [100, 77, 100],
        [95, 62, 67],
        [79, 5, 84],
        [22, 93, 46],
        [57, 34, 62],
    ];


    public function testFromWikipediaData()
    {
        $nodes = array_map(function($n) {
            return new KdNode($n);
        }, $this->wikipediaData);

        $hr = new HyperRect([0, 0], [10, 10]);

        $tree = new KdTree($hr, 2, $nodes);

        $target = new KdNode([5, 5]);
        list ($nearest, $distSqd) = $tree->nearestNeighbour($target);
        $this->assertSame([5, 4], $nearest->getPoint());
        $this->assertEquals(1, $distSqd);
    }

    public function test3D()
    {
        $nodes = array_map(function($n) {
            return new KdNode($n);
        }, $this->threeDimensions);

        $hr = new HyperRect([0, 0], [100, 100]);
        $tree = new KdTree($hr, 3, $nodes);
        $target = new KdNode([50, 50, 50]);

        list($nearest, $distSqd) = $tree->nearestNeighbour($target);
        $this->assertSame([39, 49, 43], $nearest->getPoint());
        $this->assertEquals(171, $distSqd);
    }

    public function testWithPubData()
    {
        $data = file_get_contents(__DIR__ . '/../../examples/glasgow.geojson');
        $points = json_decode($data);
        $nodes = [];
        $min = [ PHP_INT_MAX,  PHP_INT_MAX];
        $max = [-PHP_INT_MAX, -PHP_INT_MAX];

        foreach ($points->features as $index => $feature) {
            $coords = $feature->geometry->coordinates;

            for ($i = 0; $i < 1; $i++) {
                if ($coords[$i] > $max[$i]) $max[$i] = $coords[$i];
                if ($coords[$i] < $min[$i]) $min[$i] = $coords[$i];
            }

            if (!isset($feature->properties->name)) continue;
            $nodes[] = new KdNode($feature->geometry->coordinates, $feature->properties->name);
        }

        $bounds = new HyperRect($min, $max);
        $tree = new KdTree($bounds, 2, $nodes);
        $target = new KdNode([-4.284858, 55.864996]);
        list($nearest, $distSqd) = $tree->nearestNeighbour($target);
        $this->assertEquals("The Ben Nevis", $nearest->getValue());
    }
}