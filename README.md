# Open Route Service - Interface for the ORS API

ATTENTION! This v2 so far only includes the geocoding. See v1 for distance computation.

![GitHub release](https://img.shields.io/github/release/FrancoisChaumont/open-route-service.svg)
[![contributions welcome](https://img.shields.io/badge/contributions-welcome-brightgreen.svg?style=flat)](https://github.com/FrancoisChaumont/open-route-service/issues)
[![GitHub issues](https://img.shields.io/github/issues/FrancoisChaumont/open-route-service.svg)](https://github.com/FrancoisChaumont/open-route-service/issues)
[![GitHub stars](https://img.shields.io/github/stars/FrancoisChaumont/open-route-service.svg)](https://github.com/FrancoisChaumont/open-route-service/stargazers)
![Github All Releases](https://img.shields.io/github/downloads/FrancoisChaumont/open-route-service/total.svg)

PHP library to geocode addresses using the Open Route Service API.

## Getting started
These instructions will get you a copy of the project up and running on your local machine for development and testing purposes.

### Requirements
PHP 7.1+
Open Route Service API 4.5.1+

Before being able to use the API, consequently this library as well, you need to create an account with Open Route Service and request an API key. Please visit their official website in order to do so: https://open-route-service.org

### Installation
Install this package with composer by simply adding the following to your composer.json file:  
```
"repositories": [
    {
        "url": "https://github.com/FrancoisChaumont/open-route-service.git",
        "type": "git"
    }
]
```
and running the following command:  
```
composer require francoischaumont/open-route-service
```

Requires a file named [api.key](api.key) which contains the API key in order to run.

## Testing
A tests file is provided under the tests folder to give an overall idea how to use this library.
Not intented to be run.

## Built with
* Visual Studio Code
* Windows 10

## Authors
* **Francois Chaumont** - *Initial work* - [FrancoisChaumont](https://github.com/FrancoisChaumont)

See also the list of [contributors](https://github.com/FrancoisChaumont/open-route-service/graphs/contributors) who particpated in this project.

## License
This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details.

## Notes
