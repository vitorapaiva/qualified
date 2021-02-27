<?php

function find_routes(array $routes)
{
    $completeItinerary = [];
    $firstStop = '';
    $places = [];
    $linearListOfPlaces = [];

    foreach ($routes as list($origin, $destiny)) {
        $places[$origin]["to"] = $destiny;
        $places[$destiny]["from"] = $origin;
        $linearListOfPlaces[] = $origin;
        $linearListOfPlaces[] = $destiny;
    }

    foreach ($places as $key => $place) {
        if (!isset($place["from"])) {
            $firstStop = $key;
        }
    }

    $qtyStops = count($linearListOfPlaces);
    $currentPlace = $firstStop;
    $i = 0;

    while ($i < $qtyStops) {
        $completeItinerary[] = $currentPlace;
        if (!isset($places[$currentPlace]["to"])) {
            break;
        }
        $nextStep = $places[$currentPlace]["to"];
        $currentPlace = $nextStep;
        $i++;
    }

    return trim(implode(', ', $completeItinerary));
}