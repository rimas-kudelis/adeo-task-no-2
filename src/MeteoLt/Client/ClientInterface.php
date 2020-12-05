<?php
declare(strict_types=1);

namespace App\MeteoLt\Client;

use App\MeteoLt\Collection\ForecastCollection;
use App\MeteoLt\Collection\ForecastTypeCollection;
use App\MeteoLt\Collection\PlaceCollection;
use App\MeteoLt\Model\ForecastType;
use App\MeteoLt\Model\Place;

interface ClientInterface
{
    public function getPlaces(): PlaceCollection;
    public function getForecastTypes(Place $place): ForecastTypeCollection;
    public function getForecasts(Place $place, ForecastType $type): ForecastCollection;
}
