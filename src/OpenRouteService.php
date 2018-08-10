<?php

namespace FC\OpenRouteService;

/**
 * class to communicate with the Open Route Service API
 */
class OpenRouteService {

    private $apiKey;
    
    /**
     * Instantiate a new object
     *
     * @param string $apiKey API key 
     */
    function __construct(string $apiKey) {
        $this->apiKey = $apiKey;
    }

    /**
     * Geocode a given address
     *
     * @param string $address address to geocode (whole or partial address)
     * @param string $response API request response
     * @return mixed on success returns a Coordinates object
     */
    public function geocode(string $address, string &$response) {

        // define the API url adding the parameters
        $apiKey = $this->apiKey;
        $urlEncodedAddress = rawurlencode($address);
        
        $url = "https://api.openrouteservice.org/geocode/search?api_key=$apiKey&text=$urlEncodedAddress&size=1";

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

        if ($json2Obj != null && isset($json2Obj['features'])) {
            $longitude = $json2Obj['features'][0]['geometry']['coordinates'][0];
            $latitude = $json2Obj['features'][0]['geometry']['coordinates'][1];
            $coordinates = new Coordinates($longitude, $latitude);

            return $coordinates;
        }

        return null;
    }
}

