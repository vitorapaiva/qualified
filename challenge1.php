<?php
function shifted_diff(string $first, string $second): int
{
    $valid = stringsAreValid($first, $second);
    $rotationString = $first;
    $rotations = 0;
    $invalid_shift = -1;

    if ($valid) {
        while ($rotations < strlen($rotationString)) {
            if (rotationNotNeeded($first, $second)) {
                return 0;
            }
            $rotationString = rotateForward($rotationString);
            ++$rotations;
            if (isStringRotated($rotationString, $second)) {
                return $rotations;
            }
        }
    }

    return $invalid_shift;
}

function stringsAreValid(string $first, string $second): bool
{
    if (checkIfSameSize($first, $second)) {
        return false;
    }

    if (checkForEmptyStrings($first, $second)) {
        return false;
    }

    return true;
}

function checkIfSameSize(string $first, string $second): bool
{
    return strlen($first) !== strlen($second);
}

function checkForEmptyStrings(string $first, string $second): bool
{
    return empty($first) || empty($second);
}

function rotationNotNeeded(string $first, string $second): bool
{
    return $first === $second;
}

function rotateForward(string $string): string
{
    $stringAsArray = str_split($string);
    $rotatedArray = [];

    $lastValueFromArray = array_pop($stringAsArray);
    $rotatedArray[] = $lastValueFromArray;

    foreach ($stringAsArray as $letter) {
        $rotatedArray[] = $letter;
    }

    return arrayAsString($rotatedArray);
}

function isStringRotated(string $rotatingString, string $second): bool
{
    return $rotatingString === $second;
}

function arrayAsString(array $array): string
{
    return implode($array);
}

class TestShiftedDiffTest extends TestCase
{
    public function testProvidedExamples()
    {
        $this->assertEquals(2, shifted_diff("coffee", "eecoff"));
        $this->assertEquals(4, shifted_diff("eecoff", "coffee"));
        $this->assertEquals(-1, shifted_diff("moose", "Moose"));
        $this->assertEquals(2, shifted_diff("isn't", "'tisn"));
        $this->assertEquals(0, shifted_diff("Esham", "Esham"));
        $this->assertEquals(-1, shifted_diff("", ""));
        $this->assertEquals(-1, shifted_diff("dog", "god"));
    }

    /**
     ** @test
     **/
    public function stringsAreValid_checkIfDifferentSize_returnsFalse()
    {
        $result = stringsAreValid('testt', 'test');
        $this->assertFalse($result);
    }

    /**
     ** @test
     **/
    public function stringsAreValid_checkIfEmpty_returnsFalse()
    {
        $result = stringsAreValid('', '');
        $this->assertFalse($result);
    }

    /**
     ** @test
     **/
    public function rotateForward_rotateStringOneStepForward_returnsRotatedString()
    {
        $originalString = 'test';
        $expectedString = 'ttes';

        $result = rotateForward($originalString);

        $this->assertEquals($result, $expectedString);
    }
}

