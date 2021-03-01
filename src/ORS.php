<?php

namespace FC\OpenRouteService;

/**
 * class to communicate with the Open Route Service API
 */
class ORS
{
    private $apiKey;

    const PROFILE_DRIVING_CAR = 'driving-car';
    const PROFILE_DRIVING_HGV = 'driving-hgv';
    const PROFILE_CYCLING_REGULAR = 'cycling-regular';
    const PROFILE_CYCLING_ROAD = 'cycling-road';
    const PROFILE_CYCLING_SAFE = 'cycling-safe';
    const PROFILE_CYCLING_MOUNTAIN = 'cycling-mountain';
    const PROFILE_CYCLING_TOUR = 'cycling-tour';
    const PROFILE_CYCLING_ELECTRIC = 'cycling-electric';
    const PROFILE_FOOT_WALKING = 'foot-walking';
    const PROFILE_FOOT_HIKING = 'foot-hiking';
    const PROFILE_WHEELCHAIR = 'wheelchair';

    const METRICS_DISTANCE = 'distance';
    const METRICS_DURATION = 'duration';

    const UNITS_MILES = 'mi';
    const UNITS_METERS = 'm';
    const UNITS_KILOMETERS = 'km';

    const OPTIMIZED_TRUE = 'true';
    const OPTIMIZED_FALSE = 'false';

    const SOURCES_ALL = 'all';
    const DESTINATIONS_ALL = 'all';

    const GEOCODE_RESULT_SIZE = 1;
    const DISTANCE_COORDINATES_SEPARATOR = '|';
    
    /**
     * Instantiate a new object
     *
     * @param string $apiKey API key 
     */
    function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * Geocode a given address (Geocode service GET)
     *
     * @param string $response Contains either false (from curl_exec), an error message (from the API) or the entire curl_exec json response
     * @param string $address Address to geocode (whole or partial address)
     * @param int $size Limit the size of the results if the address matches multiple coordinates
     * @return Coordinates Coordinates object containing longitude and latitude
     */
    public function geocode(string &$response, string $address, int $size=self::GEOCODE_RESULT_SIZE): ?Coordinates
    {
        // define the API url adding the parameters
        $apiKey = $this->apiKey;
        $address = rawurlencode($address);
        
        $url = "https://api.openrouteservice.org/geocode/search?api_key=$apiKey&text=$address&size=$size";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Accept: application/json; charset=utf-8"));
        $response = curl_exec($ch);
        curl_close($ch);

        if (!$response) { 
            return null; 
        }

        $json2Obj = json_decode($response, true);

        if ($json2Obj != null) {
            if (isset($json2Obj['error'])) {
                $response = "[" . $json2Obj['error']['code'] . "] " . $json2Obj['error']['message'];
                return null;
            }

            if (isset($json2Obj['features'])) {
                $longitude = $json2Obj['features'][0]['geometry']['coordinates'][0];
                $latitude = $json2Obj['features'][0]['geometry']['coordinates'][1];
                $coordinates = new Coordinates($longitude, $latitude);

                return $coordinates;
            }
        }

        return null;
    }

    /**
     * Compute the distance between 2 or more locations (Matrix service GET)
     *
     * @param string $response Contains either false (from curl_exec), an error message (from the API) or the entire curl_exec json response
     * @param array $locations Pipe separated coordinates (longitude,latitude[|longitude,latitude...])
     * @param array $sources [use with destinations] Tells which locations should be computed together ("source 0 & source 1" would be: 0,1)
     * @param array $destinations [use with sources] Tells which locations should be computed together ("destination 0 & destination 1" would be: 0,1)
     * @param string $profile Type of vehicle (constants PROFILE_***)
     * @param string $units Units for the distance to retrieve (constants UNITS_***)
     * @return array Array of distances
     */
    public function distance(
        string &$response,
        array $locations,
        array $sources = self::SOURCES_ALL,
        array $destinations = self::DESTINATIONS_ALL,
        string $profile = self::PROFILE_DRIVING_CAR,
        string $units = self::UNITS_MILES): ?array
    {
        $distances = null;

        $postFields = new \stdClass();
        $postFields->locations = $locations;
        $postFields->sources = $sources;
        $postFields->destinations = $destinations;
        $postFields->units = $units;
        $postFields->metrics = [ self::METRICS_DISTANCE ];

        $url = "https://api.openrouteservice.org/v2/matrix/$profile";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Accept: application/json, application/geo+json, application/gpx+xml, img/png; charset=utf-8",
            "Authorization: " . $this->apiKey,
            "Content-Type: application/json; charset=utf-8"
        ));
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postFields));
        $response = curl_exec($ch);
        curl_close($ch);

        if (!$response) { return null; }

        $json2Obj = json_decode($response, true);
        //var_dump($json2Obj);
        if ($json2Obj != null) {
            if (isset($json2Obj['error'])) {
                $response = "[" . $json2Obj['error']['code'] . "] " . $json2Obj['error']['message'];
                return null;
            }
            
            if (isset($json2Obj['distances'])) {
                $distances = $json2Obj['distances'];
            }
        }

        return $distances;
    }
}

