<?php

function find_routes(array $routes): string
{
    $qtyStops = calculateMaxStopQty($routes);

    $places = organizeRoutesAsTree($routes);

    $startingPoint = getStartingPoint($places);

    $completeItinerary = sortRoute($qtyStops, $startingPoint, $places);

    return arrayAsString(', ', $completeItinerary);
}

function calculateMaxStopQty(array $routes): int
{
    return count($routes) * 2;
}

function organizeRoutesAsTree(array $routes): array
{
    $places = [];
    foreach ($routes as list($origin, $destiny)) {
        $places[$origin]["to"] = $destiny;
        $places[$destiny]["from"] = $origin;
    }
    return $places;
}

function getStartingPoint(array $places): string
{
    $startingPoint = '';
    $countStartingPoint = 0;
    foreach ($places as $key => $place) {
        if (!isset($place["from"])) {
            $startingPoint = $key;
            ++$countStartingPoint;
        }
    }
    if ($countStartingPoint > 1) {
        throw new InvalidArgumentException('Multiple starting points, check data');
    }
    return $startingPoint;
}

function sortRoute(int $qtyStops, string $startingPoint, array $places): array
{
    $i = 0;
    $completeItinerary = [];
    while ($i < $qtyStops) {
        $completeItinerary[] = $startingPoint;
        if (!isset($places[$startingPoint]["to"])) {
            break;
        }
        $nextStep = $places[$startingPoint]["to"];
        $startingPoint = $nextStep;
        $i++;
    }
    return $completeItinerary;
}

function arrayAsString(string $separator, array $array): string
{
    return trim(implode($separator, $array));
}

class FollowThatSpy extends TestCase
{
    public function testAlgorithm()
    {
        $testroutes1 = find_routes([["MNL", "TAG"], ["CEB", "TAC"], ["TAG", "CEB"], ["TAC", "BOR"]]);
        $this->assertEquals($testroutes1, "MNL, TAG, CEB, TAC, BOR");
        $tesroutes2 = find_routes([["Chicago", "Winnipeg"], ["Halifax", "Montreal"], ["Montreal", "Toronto"], ["Toronto", "Chicago"], ["Winnipeg", "Seattle"]]);
        $this->assertEquals($tesroutes2, "Halifax, Montreal, Toronto, Chicago, Winnipeg, Seattle");
    }

    /**
     * @test
     **/
    public function organizeRoutesAsTree_completeRoutes_returnsArray()
    {
        $input = [["MNL", "TAG"], ["CEB", "TAC"], ["TAG", "CEB"], ["TAC", "BOR"]];
        $expected = [
            'MNL' => [
                'to' => 'TAG'
            ],
            'TAG' => [
                'from' => 'MNL',
                'to' => 'CEB'
            ],
            'CEB' => [
                'from' => 'TAG',
                'to' => 'TAC'
            ],
            'TAC' => [
                'from' => 'CEB',
                'to' => 'BOR'
            ],
            'BOR' => [
                'from' => 'TAC'
            ]
        ];
        $result = organizeRoutesAsTree($input);
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     **/
    public function organizeRoutesAsTree_incompleteRoutes_returnsArray()
    {
        $input = [["MNL", "CEB"], ["CEB", "TAC"], ["TAG", "CEB"], ["TAC", "BOR"]];
        $expected = [
            'MNL' => [
                'to' => 'CEB'
            ],
            'TAG' => [
                'to' => 'CEB'
            ],
            'CEB' => [
                'from' => 'TAG',
                'to' => 'TAC'
            ],
            'TAC' => [
                'from' => 'CEB',
                'to' => 'BOR'
            ],
            'BOR' => [
                'from' => 'TAC'
            ]
        ];
        $result = organizeRoutesAsTree($input);
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     **/
    public function getStartingPoint_completeRoutes_returnsArray()
    {
        $input = [
            'MNL' => [
                'to' => 'TAG'
            ],
            'TAG' => [
                'from' => 'MNL',
                'to' => 'CEB'
            ],
            'CEB' => [
                'from' => 'TAG',
                'to' => 'TAC'
            ],
            'TAC' => [
                'from' => 'CEB',
                'to' => 'BOR'
            ],
            'BOR' => [
                'from' => 'TAC'
            ]
        ];
        $expected = 'MNL';
        $result = getStartingPoint($input);
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     **/
    public function getStartingPoint_incompleteRoutes_throwsException()
    {
        $this->expectException(InvalidArgumentException::class);
        $input = [
            'MNL' => [
                'to' => 'CEB'
            ],
            'TAG' => [
                'to' => 'CEB'
            ],
            'CEB' => [
                'from' => 'TAG',
                'to' => 'TAC'
            ],
            'TAC' => [
                'from' => 'CEB',
                'to' => 'BOR'
            ],
            'BOR' => [
                'from' => 'TAC'
            ]
        ];
        $result = getStartingPoint($input);
    }

    /**
     * @test
     **/
    public function sortRoute_completeRoutes_returnsArray()
    {
        $places = [
            'MNL' => [
                'to' => 'TAG'
            ],
            'TAG' => [
                'from' => 'MNL',
                'to' => 'CEB'
            ],
            'CEB' => [
                'from' => 'TAG',
                'to' => 'TAC'
            ],
            'TAC' => [
                'from' => 'CEB',
                'to' => 'BOR'
            ],
            'BOR' => [
                'from' => 'TAC'
            ]
        ];
        $qtyStops = 8;
        $startingPoint = 'MNL';
        $expected = ['MNL', 'TAG', 'CEB', 'TAC', 'BOR'];
        $result = sortRoute($qtyStops, $startingPoint, $places);
        $this->assertEquals($expected, $result);
    }
}