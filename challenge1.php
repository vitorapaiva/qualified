<?php
function stringsAreValid(string $first, string $second): bool
{
    if (strlen($first) !== strlen($second)) {
        return false;
    }

    if ($first === '' || $second === '') {
        return false;
    }

    return true;
}

function rotateForward(string $string): string
{
    $stringAsArray = str_split($string);
    $rotatedArray = [];

    $rotatedArray[] = array_pop($stringAsArray);
    foreach ($stringAsArray as $iValue) {
        $rotatedArray[] = $iValue;
    }

    return implode($rotatedArray);
}

function shifted_diff(string $first, string $second)
{
    $valid = stringsAreValid($first, $second);
    $rotationString = $first;
    $rotations = 0;

    if ($valid) {
        if ($first === $second) {
            return 0;
        }
        while ($rotations < strlen($rotationString)) {
            $rotationString = rotateForward($rotationString);
            ++$rotations;
            if ($rotationString === $second) {
                return $rotations;
            }
        }
    }

    return -1;
}

class TestShiftedDiff extends TestCase
{
    public function testProvidedExamples()
    {
        $this->assertEquals(2, shifted_diff("coffee", "eecoff"));
        $this->assertEquals(4, shifted_diff("eecoff", "coffee"));
        $this->assertEquals(-1, shifted_diff("moose", "Moose"));
        $this->assertEquals(2, shifted_diff("isn't", "'tisn"));
        $this->assertEquals(0, shifted_diff("Esham", "Esham"));
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
}
