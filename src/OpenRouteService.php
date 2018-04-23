<?php

    /**
     * class to communicate with the Open Route Service API
     */
    class OpenRouteService {

/** member variables */
        private $apiKey;
        private $lang;
        private $vehicle; // either 'driving-car' or 'driving-hgv'
        private $units; // either 'mi' (miles) or 'km' (kilometers)
        
        // type of vehicle
        const CAR = 'driving-car';
        const TRUCK = 'driving-hgv';
        // distance units
        const MILES = 'mi';
        const KM = 'km';
        // language
        const LANG_EN = 'en';

/** member functions */
        public function getLang(): string { return $this->lang; }
        public function getUnits(): string { return $this->units; }
        public function getVehicle(): string { return $this->vehicle; }
        public function setApiKey(string $parApiKey) { $this->apiKey = $parApiKey; }
        public function setLang(string $parLang) { $this->lang = $parLang; }
        public function setVehicle(string $parVehicle) { $this->vehicle = $parVehicle; }

/** constructor */
        /**
         * Instantiate a new object
         *
         * @param string $parApiKey API key 
         * @param string $parVehicle vehicle (either self::CAR or self::TRUCK)
         * @param string $parUnits distance units (either self::MI or self::KM)
         * @param string $parLang (default to English [en])
         */
        function __construct(string $parApiKey='', string $parVehicle=self::CAR, string $parUnits=self::MILES, string $parLang=self::LANG_EN) {
            $this->apiKey = $parApiKey;
            $this->lang = $parLang;
            $this->vehicle = $parVehicle;
            $this->units = $parUnits;
        }

/** methods */
        /**
         * Geocode a given address
         *
         * @param Address $parcAddress address to geocode
         * @param string $response API request response
         * @return mixed on success returns a Coordinates object
         */
        public function geocode(Address $parcAddress, string &$response) {
            // transform the OBJECT into a JSON string
            $queryJson = json_encode($parcAddress);
            // encode the JSON string for url purpose
            $queryJsonUrlEncoded = urlencode($queryJson);

            // define the API url adding the parameters
            $apiKey = $this->apiKey;
            $lang = $this->lang;
            $url = "https://api.openrouteservice.org/geocoding?api_key=$apiKey&query=$queryJsonUrlEncoded&lang=$lang&limit=1";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_HEADER, FALSE);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("Accept: text/json; charset=utf-8"));
            $response = curl_exec($ch);
            curl_close($ch);

            if (!$response) { return null; }

            $json2Obj = json_decode($response, true);

            if ($json2Obj != null && isset($json2Obj['features'])) {
                $longitude = $json2Obj['features'][0]['geometry']['coordinates'][0];
                $latitude = $json2Obj['features'][0]['geometry']['coordinates'][1];
                $cCoordinates = new Coordinates($longitude, $latitude);
                return $cCoordinates;
            }

            return null;
        }

        /**
         * Compute distance between 
         *
         * @param array $pararrCoordinates array of objects Coordinates
         * @param int $sources index of the array of Coordinates for the source
         * @param int $destinations index of the array of Coordinates for the destination
         * @param string $response useful to pass text for log purpose
         * @return float
         */
        public function distance(array $pararrCoordinates, int $source, int $destination, string &$response): float {
            $distance = null;

            // define the API url adding the parameters
            $apiKey = $this->apiKey;
            $vehicle = $this->vehicle;
            $units = $this->units;
            $coord = rawurlencode(Coordinates::arrToString($pararrCoordinates));
            $url = "https://api.openrouteservice.org/matrix?api_key=$apiKey&profile=$vehicle&metrics=distance&optimized=true&units=$units&sources=$source&destinations=$destination&locations=$coord";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_HEADER, FALSE);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("Accept: text/json; charset=utf-8"));
            $response = curl_exec($ch);
            curl_close($ch);

            if (!$response) { return null; }

            $json2Obj = json_decode($response, true);
            
            if ($json2Obj != null && isset($json2Obj['distances'])) {
                $distance = $json2Obj['distances'][0];
            }

            return $distance;
        }
    }


    /**
     * class to hold coordinates from geocode of address
     */
    class Coordinates {

/** member variables */
        public $longitude;
        public $latitude;

/** methods */
        /**
         * Add a Coordinates object to an array of Coordinates objects
         *
         * @param array $pararrCoordinates
         * @param Coordinates $parcCoordinates
         * @return void
         */
        public static function addCoordinatesToArray(array &$pararrCoordinates, Coordinates $parcCoordinates) {
            $arraySize = sizeof($pararrCoordinates);
            $pararrCoordinates[$arraySize] = $parcCoordinates;
        }

        /**
         * Return a string version of a Coordinates object
         * Needed to use with API
         * 
         * @param Coordinates $parcCoordinates object Coordinates
         * @param $latLongSeparator separator between latitude and longitude
         * @param $coordSeparator separator between pairs of coordinates
         * @return string
         */
        public static function cToString(Coordinates $parcCoordinates, string $latLongSeparator=',', string $coordSeparator='|'): string {
            $sCoordinates = '';

            $sCoordinates .= 
            $parcCoordinates->longitude.
            $latLongSeparator.
            $parcCoordinates->latitude;

            return $sCoordinates;
        }

        /**
         * Return a string version of array of Coordinates object
         * Needed to use with API
         * 
         * @param array array of Coordinates objects
         * @param $latLongSeparator separator between latitude and longitude
         * @param $coordSeparator separator between pairs of coordinates
         * @return string
         */
        public static function arrToString(array $pararrCoordinates, string $latLongSeparator=',', string $coordSeparator='|'): string {
            $sCoordinates = '';

            $imax = sizeof($pararrCoordinates);
            for($i = 0; $i < $imax; $i++) {
                $sCoordinates .= 
                    $pararrCoordinates[$i]->longitude.
                    $latLongSeparator.
                    $pararrCoordinates[$i]->latitude.
                    $coordSeparator;
            }

            $sCoordinates = substr($sCoordinates, 0, strlen($sCoordinates)-1);

            return $sCoordinates;
        }

/** constructor */
        /**
         * Instantiate a new object with latitude and longitude
         *
         * @param string $parLongitude longitude 
         * @param string $parLatitude latitude
         */
        function __construct(string $parLongitude, string $parLatitude) {
            $this->latitude = $parLatitude;
            $this->longitude = $parLongitude;
        }
    }


    /**
     * class to hold an address
     */
    class Address {

/** member variables */
        public $address;
        public $locality;
        public $region;
        public $postalcode;
        public $country;

/** constructor */
        /**
         * Instantiate an new object with street, locality, region, zip code and country
         *
         * @param string $parStreet street name and number
         * @param string $parLocality locality
         * @param string $parRegion region/state
         * @param string $parZipCode zip/postal code
         * @param string $parCountry country
         */
        function __construct(string $parStreet='', string $parLocality='', string $parRegion='', string $parZipCode='', string $parCountry='') {
            $this->address = $parStreet;
            $this->locality = $parLocality;
            $this->region = $parRegion;
            $this->postalcode = $parZipCode;
            $this->country = $parCountry;
        }

/** methods */
        /**
         * Return a string version of the whole address
         *
         * @param string $separator separator between each part of the address
         * @return string
         */
        public function toString(string $separator=' '): string {
            return 
                $this->address . $separator .
                $this->locality . $separator .
                $this->region . $separator .
                $this->postalcode . $separator .
                $this->country;
        }
    }
?>