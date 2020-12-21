<?php

namespace ARatnikov\DistanceToSea;

use Location\Coordinate;
use Location\Distance\Vincenty;

/**
 * Расчет ближайшего расстояния от точки до моря
 *
 * @author Andrey Ratnikov <a.ratnikov97@gmail.com>
 */
class CalculatingDistanceToSea
{
    public const SEA_OF_AZOV = 'sea_of_azov';
    public const BLACK_SEA = 'black_sea';
    public const CASPIAN_SEA = 'caspian_sea';
    public const BALTIC_SEA = 'baltic_sea';
    public const SEAS = [self::SEA_OF_AZOV, self::BLACK_SEA, self::CASPIAN_SEA, self::BALTIC_SEA];

    /**
     * @var CalculatingDistanceToSea|null
     */
    private static $instance = null;

    /**
     * @var array
     */
    private $polygons;

    public static function getInstance(): CalculatingDistanceToSea
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    private function __construct()
    {
        foreach (static::SEAS as $sea) {
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
     * Рассчитать ближайшее расстояние до определенного моря
     * @param string $nameSea - название моря
     * @param float $lat - широта
     * @param float $lng - долгота
     * @return array
     */
    public function calculateToSea(string $nameSea, float $lat, float $lng): array
    {
        return $this->getMinDistanceToSea($nameSea, $lat, $lng);
    }

    /**
     * Рассчитать ближайшее расстояние до ближайшего моря
     * @param float $lat - широта
     * @param float $lng - долгота
     * @return array
     */
    public function calculateToNearestSea(float $lat, float $lng): array
    {
        $minDistance = PHP_INT_MAX;

        $result = [];
        foreach ($this->polygons as $nameSea => $coordinates) {
            $distanceToSeas = $this->getMinDistanceToSea($nameSea, $lat, $lng);

            if ($minDistance > $distanceToSeas['distance']) {
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
    private function getMinDistanceToSea(string $nameSea, float $lat, float $lng): array
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
