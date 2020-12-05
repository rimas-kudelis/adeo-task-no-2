<?php
declare(strict_types=1);

namespace App\MeteoLt\Client;

use App\MeteoLt\Collection\ForecastCollection;
use App\MeteoLt\Collection\ForecastTypeCollection;
use App\MeteoLt\Collection\PlaceCollection;
use App\MeteoLt\Exception\MeteoLtException;
use App\MeteoLt\Model\Forecast;
use App\MeteoLt\Model\ForecastType;
use App\MeteoLt\Model\Place;
use Closure;
use DateTimeImmutable;
use Psr\Http\Client\ClientInterface as HttpClient;
use Psr\Http\Message\RequestFactoryInterface;
use Throwable;

class Client implements ClientInterface
{
    protected const API_BASE_URL = 'https://api.meteo.lt/v1';
    protected const ENDPOINT_PLACES = '/places';
    protected const ENDPOINT_PLACE = '/places/{place-code}';
    protected const ENDPOINT_TEMPLATE_FORECAST_TYPES = '/places/{place-code}/forecasts';
    protected const ENDPOINT_TEMPLATE_FORECASTS = '/places/{place-code}/forecasts/{forecast-type}';

    private HttpClient $client;
    private RequestFactoryInterface $requestFactory;

    public function __construct(HttpClient $client, RequestFactoryInterface $requestFactory)
    {
        $this->client = $client;
        $this->requestFactory = $requestFactory;
    }

    public function getPlaces(): PlaceCollection
    {
        return $this->try(function() {
            $data = $this->performRequest(static::ENDPOINT_PLACES);

            $places = new PlaceCollection();
            foreach ($data as $entry) {
                $places[] = new Place(
                    $entry['code'],
                    $entry['name'],
                    $entry['administrativeDivision'],
                    $entry['countryCode']
                );
            }

            return $places;
        });
    }

    public function getPlace(string $code): Place
    {
        return $this->try(function() use ($code) {
            $data = $this->performRequest(static::ENDPOINT_PLACE, ['place-code' => $code]);

            return new Place(
                $data['code'],
                $data['name'],
                $data['administrativeDivision'],
                $data['countryCode'],
                $data['country'],
                $data['coordinates']['latitude'],
                $data['coordinates']['longitude']
            );
        });
    }

    public function getForecastTypes(Place $place): ForecastTypeCollection
    {
        return $this->try(function() use ($place) {
            $data = $this->performRequest(static::ENDPOINT_TEMPLATE_FORECAST_TYPES, ['place-code' => $place->getCode()]);

            $types = new ForecastTypeCollection();
            foreach ($data as $entry) {
                $types[] = (new ForecastType($entry['type'], $entry['description']));
            }

            return $types;
        });
    }

    public function getForecasts(Place $place, ForecastType $type): ForecastCollection
    {
        return $this->try(function() use ($place, $type) {
            $data = $this->performRequest(static::ENDPOINT_TEMPLATE_FORECASTS, ['place-code' => $place->getCode(), 'forecast-type' => $type->getType()]);

            $forecastCreationTime = $this->convertDate($data['forecastCreationTimeUtc']);

            $forecasts = new ForecastCollection();
            foreach ($data['forecastTimestamps'] as $entry) {
                $forecasts[] = new Forecast(
                    $place,
                    $type,
                    $forecastCreationTime,
                    $this->convertDate($entry['forecastTimeUtc']),
                    $entry['airTemperature'],
                    $entry['windSpeed'],
                    $entry['windGust'],
                    $entry['windDirection'],
                    $entry['cloudCover'],
                    $entry['seaLevelPressure'],
                    $entry['relativeHumidity'],
                    $entry['totalPrecipitation'],
                    $entry['conditionCode']
                );
            }

            return $forecasts;
        });
    }

    protected function performRequest(string $endpoint, array $replacements = []): array
    {
        $request = $this->requestFactory->createRequest('GET', $this->getFullUrl($endpoint, $replacements));
        $response = $this->client->sendRequest($request);

        return json_decode($response->getBody()->getContents(), true);
    }

    protected function getFullUrl(string $endpoint, array $replacements = []): string
    {
        $replacementKeys = array_map(fn(string $key) => '{' . $key . '}', array_keys($replacements));
        $replacementValues = array_values($replacements);

        return static::API_BASE_URL . str_replace($replacementKeys, $replacementValues, $endpoint);
    }

    protected function convertDate(string $date): DateTimeImmutable
    {
        return DateTimeImmutable::createFromFormat('Y-m-d H:i:s e', $date . ' UTC');
    }

    protected function try(Closure $callable)
    {
        try {
            return $callable();
        } catch (Throwable $e) {
            throw new MeteoLtException('An error occurred while communicating with meteo.lt.', 0, $e);
        }
    }
}
