<?php

namespace ARatnikov\DistanceToSea;

use Iterator;
use Location\Coordinate;
use Location\Distance\Vincenty;

/**
 * Calculation of the nearest distance to the sea
 *
 * @author Andrey Ratnikov <a.ratnikov97@gmail.com>
 */
class Calculating
{
    /**
     * @var Calculating|null
     */
    private static $instance = null;

    public static function getInstance(): self
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    /**
     * Calculate to the sea
     * @param string $nameSea
     * @param float $lat
     * @param float $lng
     * @return Distance
     */
    public function calculateToSea(string $nameSea, float $lat, float $lng): Distance
    {
        return $this->calculateNearestDistanceToSea($nameSea, $lat, $lng);
    }

    /**
     * Calculate to the nearest sea
     * @param float $lat
     * @param float $lng
     * @return Distance
     */
    public function calculateToNearestSea(float $lat, float $lng): Distance
    {
        $minDistance = PHP_INT_MAX;

        $result = [];
        foreach (Seas::ALL as $sea) {
            $distanceToSea = $this->calculateNearestDistanceToSea($sea, $lat, $lng);

            if ($minDistance > $distanceToSea->getDistance()) {
                $minDistance = $distanceToSea->getDistance();
                $result = $distanceToSea;
            }
        }

        return $result;
    }

    /**
     * Calculate the nearest distance to the sea
     * @param string $nameSea
     * @param float $lat
     * @param float $lng
     * @return Distance
     */
    private function calculateNearestDistanceToSea(string $nameSea, float $lat, float $lng): Distance
    {
        $minDistance = PHP_INT_MAX;

        $nearestCoordinates = [];
        foreach ($this->preparePolygonOfSea($nameSea) as [$pLng, $pLat]) {
            $coordinate1 = new Coordinate($lat, $lng);
            $coordinate2 = new Coordinate($pLat, $pLng);

            $distance = (new Vincenty())->getDistance($coordinate1, $coordinate2);

            if ($minDistance > $distance) {
                $minDistance = $distance;
                $nearestCoordinates = [$pLat, $pLng];
            }
        }

        return new Distance($nameSea, round($minDistance, 1), $nearestCoordinates);
    }

    /**
     * Prepare the sea polygon (GeoJson: GeometryCollection)
     * @param string $name
     * @return Iterator
     */
    private function preparePolygonOfSea(string $name): Iterator
    {
        $path = dirname(__DIR__) . "/data/$name.json";
        $data = json_decode(file_get_contents($path), true);
        $coordinates = (array)$data['geometries'][0]['coordinates'];

        $iterator = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($coordinates));

        foreach ($iterator as $ignored) {
            yield (array)$iterator->getInnerIterator();
            $iterator->next();
        }
    }
}
