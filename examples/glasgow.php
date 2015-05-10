<?php
/**
 * The data used by this example was generated from the Overpass API
 * instance at http://overpass-turbo.eu/ using the following query:
 *
 * node
 *   [amenity=pub]
 *   ({{bbox}});
 * out;
 */

use MGDM\DataStructures\HyperRect;
use MGDM\DataStructures\KdNode;
use MGDM\DataStructures\KdTree;

require (__DIR__ . '/../vendor/autoload.php');

$data = file_get_contents(__DIR__ . '/glasgow.geojson');
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
var_dump($tree->nearestNeighbour($target)[0]->getValue());
