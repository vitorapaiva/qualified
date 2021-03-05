<?php

function find_routes(array $routes): string
{
    $completeItinerary = [];

    $places = organizeRoutes($routes);
    $startingPoint = getStartingPoint($places);
    $completeItinerary = sortRoute($startingPoint, $completeItinerary, $places);

    return arrayAsString(', ', $completeItinerary);
}

function organizeRoutes(array $routes): array
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

function sortRoute(string $startingPoint, array $completeItinerary, array $places): array
{
    $completeItinerary[] = $startingPoint;
    if (!isset($places[$startingPoint]["to"])) {
        return $completeItinerary;
    }
    $nextStep = $places[$startingPoint]["to"];
    return sortRoute($nextStep, $completeItinerary, $places);
}

function arrayAsString(string $separator, array $array): string
{
    return trim(implode($separator, $array));
}

class FollowThatSpyTest extends TestCase
{
    public function testAlgorithm()
    {
        $testroutes1 = find_routes([["MNL", "TAG"], ["CEB", "TAC"], ["TAG", "CEB"], ["TAC", "BOR"]]);
        $this->assertEquals($testroutes1, "MNL, TAG, CEB, TAC, BOR");
        $tesroutes2 = find_routes([["Chicago", "Winnipeg"], ["Halifax", "Montreal"], ["Montreal", "Toronto"], ["Toronto", "Chicago"], ["Winnipeg", "Seattle"]]);
        $this->assertEquals($tesroutes2, "Halifax, Montreal, Toronto, Chicago, Winnipeg, Seattle");
        //$testroutes3 = find_routes([["MNL", "TAG"], ["CEB", "TAC"], ["TAG", "CEB"], ["TAC", "BOR"], ["BOR", "MNL"], ["MNL", "TAG"]]);
        //$this->assertEquals($testroutes3, "MNL, TAG, CEB, TAC, BOR, MNL, TAG");

    }

    /**
     * @test
     **/
    public function organizeRoutes_completeRoutes_returnsArray()
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
        $result = organizeRoutes($input);
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     **/
    public function organizeRoutes_incompleteRoutes_returnsArray()
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
        $result = organizeRoutes($input);
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

        $startingPoint = 'MNL';
        $expected = ['MNL', 'TAG', 'CEB', 'TAC', 'BOR'];
        $result = sortRoute($startingPoint, [], $places);
        $this->assertEquals($expected, $result);
    }
}