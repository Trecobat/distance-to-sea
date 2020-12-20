<?php

namespace ARatnikov\DistanceToSea;

use RecursiveArrayIterator;
use RecursiveIteratorIterator;
use Location\Coordinate;
use Location\Distance\Vincenty;

/**
 * Расчет ближайшего расстояния от точки до моря
 *
 * @author Andrey Ratnikov <a.ratnikov97@gmail.com>
 */
class DistanceToSea
{
    const SEAS = ['sea_of_azov', 'black_sea', 'caspean_sea', 'baltic_sea'];

    /**
     * @var DistanceToSea|null
     */
    private static $instance = null;

    /**
     * @var array
     */
    private $polygons;

    public static function getInstance(): DistanceToSea
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    private function __construct()
    {
        foreach (static::SEAS as $sea) {
            $this->polygons[$sea] = $this->_preparePolygonOfSea($sea);
        }
    }

    private function __clone() {}
    private function __wakeup() {}

    /**
     * Рассчитать ближайшее расстояние до определенного моря
     * @param string $nameSea - название моря
     * @param float $lat - широта
     * @param float $lng - долгота
     * @return array
     */
    public function calculateToSea(string $nameSea, float $lat, float $lng)
    {
        return $this->_getMinDistanceToSea($lat, $lng, $nameSea);
    }

    /**
     * Рассчитать ближайшее расстоние до ближайшего моря
     * @param float $lat - широта
     * @param float $lng - долгота
     * @return array
     */
    public function calculateToNearestSea(float $lat, float $lng)
    {
        $minDistance = PHP_INT_MAX;

        $result = [];
        foreach($this->polygons as $nameSea => $coordinates) {
            $distanceToSeas = $this->_getMinDistanceToSea($lat, $lng, $nameSea);

            if ($minDistance > $distanceToSeas['distance']){
                $result = $distanceToSeas;
            }
        }

        return $result;
    }

    /**
     * Получить ближайшее расстояние до моря
     * @param string $nameSea - название моря
     * @param float $lat - широта
     * @param float $lng - долгота
     * @return array
     */
    private function _getMinDistanceToSea(string $nameSea, float $lat, float $lng)
    {
        $minDistance = PHP_INT_MAX;

        $nearestCoordinates = [];
        foreach($this->polygons[$nameSea] as list($pLng, $pLat)){
            $coordinate1 = new Coordinate($lat, $lng);
            $coordinate2 = new Coordinate($pLat, $pLng);

            $distance = (new Vincenty())->getDistance($coordinate1, $coordinate2);

            if ($minDistance > $distance){
                $minDistance = $distance;
                $nearestCoordinates = [$pLat, $pLng];
            }
        }

        return [
            'name'        => $nameSea,
            'distance'    => $minDistance,
            'coordinates' => $nearestCoordinates,
        ];
    }

    /**
     * Получить координаты береговой границы моря (тип GeoJson: GeometryCollection)
     * @param string $name название моря
     * @return array
     */
    private function _preparePolygonOfSea($name)
    {
        $path = dirname(__DIR__)."/data/{$name}.json";
        $coordinates = json_decode(file_get_contents($path), true);

        $iterator = new RecursiveIteratorIterator(
            new RecursiveArrayIterator((array)$coordinates['geometries'][0]['coordinates']));

        $result = [];
        foreach($iterator as $value){
            $result[] = (array)$iterator->getInnerIterator();
            $iterator->next();
        }

        return $result;
    }
}