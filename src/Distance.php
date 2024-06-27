<?php

declare(strict_types=1);

namespace Trecobat\DistanceToSea;

/**
 *
 * @author Andrey Ratnikov <a.ratnikov97@gmail.com>
 */
class Distance
{
    /**
     * @var string
     */
    private $seaName;

    /**
     * @var float
     */
    private $distance;

    /**
     * @var array
     */
    private $nearestCoordinates;

    public function __construct(string $seaName, float $distance, array $nearestCoordinates)
    {
        $this->seaName = $seaName;
        $this->distance = $distance;
        $this->nearestCoordinates = $nearestCoordinates;
    }

    public function getSeaName(): string
    {
        return $this->seaName;
    }

    public function getDistance(): float
    {
        return $this->distance;
    }

    public function getNearestCoordinates(): array
    {
        return $this->nearestCoordinates;
    }
}
