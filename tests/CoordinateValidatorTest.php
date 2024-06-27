<?php

namespace Trecobat\DistanceToSea;

use PHPUnit\Framework\TestCase;
use UnexpectedValueException;

class CoordinateValidatorTest extends TestCase
{
    public function testValidate()
    {
        $this->assertTrue(CoordinateValidator::validate(44.47755606247829, 34.145802750750015));
    }

    public function testException()
    {
        $this->expectException(UnexpectedValueException::class);
        CoordinateValidator::validate(90.01, 45.0001);

        $this->expectException(UnexpectedValueException::class);
        CoordinateValidator::validate(45.01, 190.00);
    }
}
