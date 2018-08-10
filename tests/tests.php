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

// address (whole or partial)
$address = "Chicago, USA";

// instantiate new object with the API key
$cOrs = new OpenRouteService($apiKey);

// geocode the address
$coordinates = $cOrs->geocode($address, $txtForLogs);
if ($coordinates == null) { 
    die("Error geocoding address".PHP_EOL.$txtForLogs); 
}

printf(" Latitude: %s\n Lontitude: %s", $coordinates->latitude, $coordinates->longitude);

