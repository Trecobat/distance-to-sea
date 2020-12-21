<?php

namespace ARatnikov\DistanceToSea;

use PHPUnit\Framework\TestCase;

class CalculatingTest extends TestCase
{
    public function testCalculateToSea(): void
    {
        $calculating = Calculating::getInstance();

        $lat = 44.47755606247829;
        $lng = 34.145802750750015;

        $result = $calculating->calculateToSea(Seas::BLACK_SEA, $lat, $lng);

        $this->assertEquals(723.5, $result->getDistance());
    }

    public function testCalculateToNearestSea(): void
    {
        $calculating = Calculating::getInstance();

        $lat = 44.451635;
        $lng = 34.060591;

        $result = $calculating->calculateToNearestSea($lat, $lng);

        $this->assertEquals(3229.2, $result->getDistance());
    }
}
