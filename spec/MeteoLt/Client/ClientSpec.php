<?php

namespace spec\App\MeteoLt\Client;

use App\MeteoLt\Client\Client;
use App\MeteoLt\Client\ClientInterface;
use App\MeteoLt\Collection\ForecastCollection;
use App\MeteoLt\Collection\ForecastTypeCollection;
use App\MeteoLt\Collection\PlaceCollection;
use App\MeteoLt\Model\Forecast;
use App\MeteoLt\Model\ForecastType;
use App\MeteoLt\Model\Place;
use ArrayIterator;
use DateTimeImmutable;
use PhpSpec\ObjectBehavior;
use Psr\Http\Client\ClientInterface as PsrHttpClient;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class ClientSpec extends ObjectBehavior
{
    function let(PsrHttpClient $client, RequestFactoryInterface $requestFactory): void
    {
        $this->beConstructedWith($client, $requestFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Client::class);
        $this->shouldImplement(ClientInterface::class);
    }

    function it_returns_places($client, $requestFactory, RequestInterface $request, ResponseInterface $response, StreamInterface $body)
    {
        $requestFactory->createRequest('GET', 'https://api.meteo.lt/v1/places')->shouldBeCalled()
            ->willReturn($request);

        $client->sendRequest($request)->shouldBeCalled()
            ->willReturn($response);

        $response->getBody()->shouldBeCalled()
            ->willReturn($body);

        $body->getContents()->shouldBeCalled()
            ->willReturn('[
                {
                    "code":"abromiskes",
                    "name":"Abromi\u0161k\u0117s",
                    "administrativeDivision":"Elektr\u0117n\u0173 savivaldyb\u0117",
                    "countryCode":"LT"
                }, {
                    "code":"vilnius",
                    "name":"Vilnius",
                    "administrativeDivision":"Vilniaus miesto savivaldyb\u0117",
                    "countryCode":"LT"
                }
            ]');

        $places = $this->getPlaces();
        $places->shouldHaveType(PlaceCollection::class);
        $places->shouldHaveCount(2);
        $places->shouldYieldLike(new ArrayIterator([
            new Place('abromiskes', 'Abromiškės', 'Elektrėnų savivaldybė', 'LT'),
            new Place('vilnius', 'Vilnius', 'Vilniaus miesto savivaldybė', 'LT'),
        ]));
    }

    function it_should_return_forecast_types(
        $client,
        $requestFactory,
        Place $place,
        RequestInterface $request,
        ResponseInterface $response,
        StreamInterface $body
    ) {
        $place->getCode()->shouldBeCalled()
            ->willReturn('abromiskes');

        $requestFactory->createRequest('GET', 'https://api.meteo.lt/v1/places/abromiskes/forecasts')->shouldBeCalled()
            ->willReturn($request);

        $client->sendRequest($request)->shouldBeCalled()
            ->willReturn($response);

        $response->getBody()->shouldBeCalled()
            ->willReturn($body);

        $body->getContents()->shouldBeCalled()
            ->willReturn('[
                {
                    "type":"long-term",
                    "description":"Long term numerical weather prediction"
                }, {
                    "type":"test",
                    "description":"Test description"
                }
            ]');

        $forecastTypes = $this->getForecastTypes($place);
        $forecastTypes->shouldHaveType(ForecastTypeCollection::class);
        $forecastTypes->shouldHaveCount(2);
        $forecastTypes->shouldYieldLike(new ArrayIterator([
            new ForecastType('long-term', 'Long term numerical weather prediction'),
            new ForecastType('test', 'Test description'),
        ]));
    }

    function it_should_return_forecasts(
        $client,
        $requestFactory,
        Place $place,
        ForecastType $type,
        RequestInterface $request,
        ResponseInterface $response,
        StreamInterface $body
    ) {
        $place->getCode()->shouldBeCalled()
            ->willReturn('gargzdai');

        $type->getType()->shouldBeCalled()
            ->willReturn('new-type');

        $requestFactory->createRequest('GET', 'https://api.meteo.lt/v1/places/gargzdai/forecasts/new-type')->shouldBeCalled()
            ->willReturn($request);

        $client->sendRequest($request)->shouldBeCalled()
            ->willReturn($response);

        $response->getBody()->shouldBeCalled()
            ->willReturn($body);

        $body->getContents()->shouldBeCalled()
            ->willReturn('{
                "place": {
                    "code":"gargzdai",
                    "name":"Garg\u017edai",
                    "administrativeDivision":"Klaip\u0117dos rajono savivaldyb\u0117",
                    "country":"Lietuva",
                    "countryCode":"LT",
                    "coordinates":{
                        "latitude":55.71322,
                        "longitude":21.385565
                    }
                },
                "forecastType":"new-type",
                "forecastCreationTimeUtc":"2020-12-05 11:28:25",
                "forecastTimestamps":[
                    {
                        "forecastTimeUtc":"2020-12-05 13:00:00",
                        "airTemperature":6.9,
                        "windSpeed":8,
                        "windGust":14,
                        "windDirection":141,
                        "cloudCover":7,
                        "seaLevelPressure":1012,
                        "relativeHumidity":90,
                        "totalPrecipitation":0,
                        "conditionCode":"clear"
                    }, {
                        "forecastTimeUtc":"2020-12-05 14:00:00",
                        "airTemperature":6.7,
                        "windSpeed":8,
                        "windGust":14,
                        "windDirection":142,
                        "cloudCover":27,
                        "seaLevelPressure":1012,
                        "relativeHumidity":91,
                        "totalPrecipitation":0,
                        "conditionCode":"isolated-clouds"
                    }, {
                        "forecastTimeUtc":"2020-12-05 15:00:00",
                        "airTemperature":6.6,
                        "windSpeed":8,
                        "windGust":14,
                        "windDirection":143,
                        "cloudCover":20,
                        "seaLevelPressure":1013,
                        "relativeHumidity":92,
                        "totalPrecipitation":0,
                        "conditionCode":"isolated-clouds"
                    }
                ]
            }');

        $forecasts = $this->getForecasts($place, $type);

        $forecasts->shouldHaveType(ForecastCollection::class);
        $forecasts->shouldHaveCount(3);

        $forecastCreationTime = new DateTimeImmutable('2020-12-05T11:28:25+00:00');
        $realPlace = $place->getWrappedObject();
        $realType = $type->getWrappedObject();
        $forecasts->shouldYieldLike(new ArrayIterator([
            new Forecast(
                $realPlace,
                $realType,
                $forecastCreationTime,
                new DateTimeImmutable('2020-12-05T13:00:00+00:00'),
                6.9,
                8,
                14,
                141,
                7,
                1012,
                90,
                0,
                'clear'
            ),
            new Forecast(
                $realPlace,
                $realType,
                $forecastCreationTime,
                new DateTimeImmutable('2020-12-05T14:00:00+00:00'),
                6.7,
                8,
                14, 142,
                27,
                1012,
                91,
                0,
                'isolated-clouds'
            ),
            new Forecast(
                $realPlace,
                $realType,
                $forecastCreationTime,
                new DateTimeImmutable('2020-12-05T15:00:00+00:00'),
                6.6,
                8,
                14,
                143,
                20,
                1013,
                92,
                0,
                'isolated-clouds'
            ),
        ]));
    }
}
