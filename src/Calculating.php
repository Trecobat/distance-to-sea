<?php

namespace ARatnikov\DistanceToSea;

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

    /**
     * @var array
     */
    private $polygons;

    public static function getInstance(): Calculating
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    private function __construct()
    {
        foreach (Seas::ALL as $sea) {
            $this->polygons[$sea] = $this->preparePolygonOfSea($sea);
        }
    }

    private function __clone()
    {
    }
    private function __wakeup()
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
        foreach ($this->polygons as $nameSea => $coordinates) {
            $distanceToSeas = $this->calculateNearestDistanceToSea($nameSea, $lat, $lng);

            if ($minDistance > $distanceToSeas->getDistance()) {
                $minDistance = $distanceToSeas->getDistance();
                $result = $distanceToSeas;
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
        foreach ($this->polygons[$nameSea] as list($pLng, $pLat)) {
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
     * @return array
     */
    private function preparePolygonOfSea(string $name): array
    {
        $path = dirname(__DIR__) . "/data/{$name}.json";
        $data = json_decode(file_get_contents($path), true);
        $coordinates = (array)$data['geometries'][0]['coordinates'];

        $iterator = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($coordinates));

        $result = [];
        foreach ($iterator as $value) {
            $result[] = (array)$iterator->getInnerIterator();
            $iterator->next();
        }

        return $result;
    }
}
