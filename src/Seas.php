<?php

declare(strict_types=1);

namespace Trecobat\DistanceToSea;

/**
 * @author Andrey Ratnikov <a.ratnikov97@gmail.com>
 */
class Seas
{
    public const SEA_OF_AZOV = 'sea_of_azov';
    public const BLACK_SEA = 'black_sea';
    public const CASPIAN_SEA = 'caspian_sea';
    public const BALTIC_SEA = 'baltic_sea';
    public const FRANCE_SEA = 'france_sea';
    public const ALL = [self::SEA_OF_AZOV, self::BLACK_SEA, self::CASPIAN_SEA, self::BALTIC_SEA, self::FRANCE_SEA];
}
