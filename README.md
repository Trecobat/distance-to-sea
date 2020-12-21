# Distance to seas
Calculation of the nearest distance to the sea.

## Requirements
Minimum required PHP version is 7.2.

## Installation

Using [Composer](https://getcomposer.org), just add it to your `composer.json` by running:

```
composer require aratnikov/distance-to-sea 
```

## Examples/Usage
```php
<?php

use ARatnikov\DistanceToSea\CalculatingDistanceToSea;

$calculating = CalculatingDistanceToSea::getInstance();

$lat = 44.47755606247829;
$lng = 34.145802750750015;

//to the nearest sea
$result = $calculating->calculateToNearestSea($lat, $lng);

//or to a certain sea
$result = $calculating->calculateToSea(CalculatingDistanceToSea::BLACK_SEA, $lat, $lng);

echo "To the {$result['name']} {$result['distance']} meters"; // To the black_sea 700 meters
```

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.