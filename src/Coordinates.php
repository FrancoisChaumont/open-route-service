<?php

namespace FC\OpenRouteService;

/**
 * class to hold coordinates from geocode of address
 */
class Coordinates {

    public $longitude;
    public $latitude;

    /**
     * Instantiate a new object with latitude and longitude
     *
     * @param string $longitude longitude 
     * @param string $latitude latitude
     */
    function __construct(string $longitude, string $latitude) {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }
}

