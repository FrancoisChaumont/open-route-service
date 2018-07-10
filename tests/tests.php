<?php

namespace FC\OpenRouteService;

require __DIR__."/../vendor/autoload.php";

$sTxtForLogs = '';
$response = '';

// your API key
$sApiKey = '123';

// source address
$sourceStreet = '4046 N Leavitt St';
$sourceLocality = 'Chicago';
$sourceRegion = 'IL';
$sourceZipCode = '60618';
$sourceCountry = 'USA';

// destination address
$destStreet = '5454 N Wolcott Ave';
$destLocality = 'Chicago';
$destRegion = 'IL';
$destZipCode = '60640';
$destCountry = 'USA';

$cOrs = new OpenRouteService($sApiKey);

$source = 0;
// create a new object address with the source location
$cAddrSource = new Address($sourceStreet, $sourceLocality, $sourceRegion, $sourceZipCode, $sourceCountry);
// geocode the source address
$cCoordSource = $cOrs->geocode($cAddrSource, $sTxtForLogs);
if ($cCoordSource == null) { die("Error geocoding home location".PHP_EOL.$sTxtForLogs); }

$destination = 1;
// create a new object address with the destination location
$cAddrDest = new Address($destStreet, $destLocality, $destRegion, $destZipCode, $destCountry);
// geocode the destination address
$cCoordDest = $cOrs->geocode($cAddrDest, $sTxtForLogs);
if ($cCoordDest == null) { die("Error geocoding home location".PHP_EOL.$sTxtForLogs); }

// create an array to contain both coordinates objects
$arrCoordinates[] = $cCoordSource;
$arrCoordinates[] = $cCoordDest;

// compute distance between source and destination
$distances = $cOrs->distance($arrCoordinates, $source, $destination, $response);
if ($distances != null && isset($distances)) {
    echo "distance from $source to $destination: ".$distances[0]." ".$cOrs->getUnits();
}

