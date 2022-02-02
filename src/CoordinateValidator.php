<?php

declare(strict_types=1);

namespace ARatnikov\DistanceToSea;

use UnexpectedValueException;

/**
 * Coordinate validation
 *
 * @author Andrey Ratnikov <a.ratnikov97@gmail.com>
 */
final class CoordinateValidator
{
    private const MAX_LAT = 90;
    private const MAX_LNG = 180;

    /**
     * Validation of latitude and longitude values
     * @param float $lat
     * @param float $lng
     * @return bool
     */
    public static function validate(float $lat, float $lng): bool
    {
        if (abs($lat) > self::MAX_LAT) {
            throw new UnexpectedValueException('Latitude must be between -90 and 90');
        }

        if (abs($lng) > self::MAX_LNG) {
            throw new UnexpectedValueException('Longitude must be between -180 and 180');
        }

        return true;
    }
}