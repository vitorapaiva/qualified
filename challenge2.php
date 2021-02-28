<?php

function orderWeight($str)
{
    $nums = explode(" ", $str);

    $valid = validateNumberList($nums);
    if ($valid) {
        $listSize = count($nums);
        for ($i = 0; $i < $listSize; $i++) {
            for ($j = 0; $j < $listSize - 1; $j++) {
                $currentNumber = $nums[$j];
                $nextNumber = $nums[$j + 1];

                if (shouldSwapPositions($currentNumber, $nextNumber)) {
                    $nums[$j] = $nextNumber;
                    $nums[$j + 1] = $currentNumber;
                }
            }
        }
    }

    return implode(' ', $nums);
}

function validateNumberList(array $numberList): bool
{
    foreach ($numberList as $numberAsString) {
        $number = (int)$numberAsString;

        if ((string)$number !== (string)$numberAsString) {
            return false;
        }

        if ($number < 0) {
            return false;
        }

        if (!is_int($number)) {
            return false;
        }
    }
    return true;
}

function shouldSwapPositions(string $currentNumber, string $nextNumber): bool
{
    $currentWeight = calculateStringWeight($currentNumber);
    $nextWeight = calculateStringWeight($nextNumber);
    if ($currentWeight > $nextWeight) {
        return true;
    }

    if ($currentWeight === $nextWeight) {
        $result = checkIfNextComesFirst($currentNumber, $nextNumber);
        if ($result) {
            return true;
        }
    }
    return false;
}

function calculateStringWeight(string $number): int
{
    $digitArray = str_split($number);
    $stringWeight = 0;

    foreach ($digitArray as $digit) {
        $stringWeight += $digit;
    }

    return $stringWeight;
}

function checkIfNextComesFirst(string $firstNumber, string $nextNumber)
{
    $firstAsArray = str_split($firstNumber);
    $nextAsArray = str_split($nextNumber);
    $firstArraySize = count($firstAsArray);
    $nextArraySize = count($nextAsArray);
    $size = $firstArraySize;

    for ($i = 0; $i < $size; $i++) {
        if (!isset($firstAsArray[$i])) {
            return false;
        }
        if (!isset($nextAsArray[$i])) {
            return true;
        }
        if ($firstAsArray[$i] > $nextAsArray[$i]) {
            return true;
        }
        if ($firstAsArray[$i] < $nextAsArray[$i]) {
            return false;
        }
    }

    return false;
}

class OrderWeightTestCases extends TestCase
{
    public function testBasics()
    {
        $this->assertEquals("2 2000 103 123 4444 99", orderWeight("2000 103 2 123 4444 99"));
        $this->assertEquals("2000 103 123 4444 99", orderWeight("103 123 4444 99 2000"));
        $this->assertEquals("11 11 2000 10003 22 123 1234000 44444444 9999", orderWeight("2000 10003 1234000 44444444 9999 11 11 22 123"));
    }

    /**
     * @test
     **/
    public function calculateStringWeight_validString_returnsTotal()
    {
        $string = '2000';
        $expectedWeight = 2;
        $result = calculateStringWeight($string);

        $this->assertEquals($expectedWeight, $result);
    }

    /**
     * @test
     **/
    public function calculateStringWeight_ZeroAsString_returnsZero()
    {
        $string = '0';
        $expectedWeight = 0;
        $result = calculateStringWeight($string);

        $this->assertEquals($expectedWeight, $result);
    }

    /**
     * @test
     **/
    public function calculateStringWeight_singleDigitAsString_returnsSameDigit()
    {
        $string = '2';
        $expectedWeight = 2;
        $result = calculateStringWeight($string);

        $this->assertEquals($expectedWeight, $result);
    }

    /**
     * @test
     **/
    public function validateNumberList_validNumberList_returnsTrue()
    {
        $numberList = ["11", "22", "33"];
        $result = validateNumberList($numberList);
        $this->assertEquals(true, $result);
    }

    /**
     * @test
     **/
    public function validateNumberList_negativeNumber_returnsFalse()
    {
        $numberList = ["11", "-22", "33"];
        $result = validateNumberList($numberList);
        $this->assertEquals(false, $result);
    }

    /**
     * @test
     **/
    public function validateNumberList_floatNumber_returnsFalse()
    {
        $numberList = ["11", "0.5", "33"];
        $result = validateNumberList($numberList);
        $this->assertEquals(false, $result);
    }

    /**
     * @test
     **/
    public function validateNumberList_wrongFormatFloatNumber_returnsFalse()
    {
        $numberList = ["11", "33", "0,5"];
        $result = validateNumberList($numberList);
        $this->assertEquals(false, $result);
    }

    /**
     * @test
     **/
    public function validateNumberList_stringInsteadOfNumber_returnsFalse()
    {
        $numberList = ["11", "33", "Test", "44"];
        $result = validateNumberList($numberList);
        $this->assertEquals(false, $result);
    }

    /**
     * @test
     **/
    public function checkIfNextComesFirst_firstDigitSmaller_returnsTrue()
    {
        $firstNumber = '90';
        $nextNumber = '180';
        $result = checkIfNextComesFirst($firstNumber, $nextNumber);
        $this->assertEquals(true, $result);
    }

    /**
     * @test
     **/
    public function checkIfNextComesFirst_secondDigitSmaller_returnsTrue()
    {
        $firstNumber = '120';
        $nextNumber = '111';
        $result = checkIfNextComesFirst($firstNumber, $nextNumber);
        $this->assertEquals(true, $result);
    }

    /**
     * @test
     **/
    public function checkIfNextComesFirst_lastDigitBigger_returnsFalse()
    {
        $firstNumber = '190';
        $nextNumber = '191';
        $result = checkIfNextComesFirst($firstNumber, $nextNumber);
        $this->assertEquals(false, $result);
    }

    /**
     * @test
     **/
    public function shouldSwapPositions_firstLighter_returnFalse()
    {
        $firstNumber = '11';
        $nextNumber = '19';
        $result = shouldSwapPositions($firstNumber, $nextNumber);
        $this->assertEquals(false, $result);
    }

    /**
     * @test
     **/
    public function shouldSwapPositions_secondLighter_returnTrue()
    {
        $firstNumber = '011';
        $nextNumber = '01';
        $result = shouldSwapPositions($firstNumber, $nextNumber);
        $this->assertEquals(true, $result);
    }

    /**
     * @test
     **/
    public function shouldSwapPositions_sameWeightDiffStrings_returnTrue()
    {
        $firstNumber = '200';
        $nextNumber = '2';
        $result = shouldSwapPositions($firstNumber, $nextNumber);
        $this->assertEquals(true, $result);
    }
}