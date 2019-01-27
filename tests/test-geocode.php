<?php

namespace FC\OpenRouteService;

require __DIR__."/../vendor/autoload.php";

$response = '';
$apiKeyFile = __DIR__ . "/../api.key";

// your API key
$apiKey = file_get_contents($apiKeyFile);
if ($apiKey == '') {
    die("No API key!");
}

// address (whole or partial)
$address = "3181 N Broadway, Chicago, IL 60657";

// instantiate new object with the API key
$cOrs = new ORS($apiKey);

// geocode the address
$coordinates = $cOrs->geocode($response, $address);
if ($coordinates == null) { 
    die("Error geocoding address: " . $response); 
}

printf("Lontitude: %s\nLatitude: %s\n", $coordinates->longitude, $coordinates->latitude);

