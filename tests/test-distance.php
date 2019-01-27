<?php

namespace FC\OpenRouteService;

require __DIR__."/../vendor/autoload.php";

$txtForLogs = '';
$response = '';
$apiKeyFile = __DIR__ . "/../api.key";

// your API key
$apiKey = file_get_contents($apiKeyFile);
if ($apiKey == '') {
    die("No API key!");
}

$locations = "-87.644234,41.939932|-87.677394,41.929345";

$sources = '0';
$destinations = '1';
$profile = ORS::PROFILE_DRIVING_CAR;
$units = ORS::UNITS_MILES;
$optimized = ORS::OPTIMIZED_TRUE;

$response = '';

// instantiate new object with the API key
$cOrs = new ORS($apiKey);

// geocode the address
$distances = $cOrs->distance($response, $locations, $sources, $destinations, $profile, $units, $optimized);
if ($distances == null) { 
    die("Error computing distance: $response\n");
}

print_r($distances);

