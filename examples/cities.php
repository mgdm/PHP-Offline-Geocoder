<?php
/**
 * The data used by this example was ultimately taken from Geonames, but this processed
 * version is from the Python reverse geocoder at
 * https://github.com/thampiman/reverse-geocoder/
 */

use MGDM\DataStructures\HyperRect;
use MGDM\DataStructures\KdNode;
use MGDM\DataStructures\KdTree;

require (__DIR__ . '/../vendor/autoload.php');
ini_set('memory_limit', -1);

define("CACHE_FILE", __DIR__ . "/citiescache.sphp");
define("INPUT_FILE", __DIR__ . "/rg_cities1000.csv");

$nodes = [];
$min = [ PHP_INT_MAX,  PHP_INT_MAX];
$max = [-PHP_INT_MAX, -PHP_INT_MAX];

if (file_exists(CACHE_FILE)) {
    $tree = unserialize(file_get_contents(CACHE_FILE));
} else {
    $input = fopen(INPUT_FILE, "rb");
    $headers = fgetcsv($input);

    while (($row = fgetcsv($input)) !== false) {
        for ($i = 0; $i < 1; $i++) {
            if ($row[$i] > $max[$i]) $max[$i] = $row[$i];
            if ($row[$i] < $min[$i]) $min[$i] = $row[$i];
        }

        $nodes[] = new KdNode([$row[0], $row[1]], array_combine($headers, $row));
    }

    $bounds = new HyperRect($min, $max);
    $tree = new KdTree($bounds, 2, $nodes);
    file_put_contents(CACHE_FILE, serialize($tree));
}

$target = new KdNode([55.864996, -4.284858]);
var_dump($tree->nearestNeighbour($target)[0]->getValue());
printf ("Used %d bytes\n", memory_get_peak_usage());
