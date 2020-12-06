<?php

namespace spec\App\Service;

use App\Collection\RecommendationCollection;
use App\Entity\Product;
use App\MeteoLt\Client\ClientInterface;
use App\MeteoLt\Collection\ForecastCollection;
use App\MeteoLt\Collection\ForecastTypeCollection;
use App\MeteoLt\Model\Forecast;
use App\MeteoLt\Model\ForecastType;
use App\MeteoLt\Model\Place;
use App\Model\LocationAwareRecommendations;
use App\Repository\ProductRepository;
use App\Service\WeatherForecastDependentProductRecommender;
use DateTimeImmutable;
use PhpSpec\ObjectBehavior;

class WeatherForecastDependentProductRecommenderSpec extends ObjectBehavior
{
    const FORECAST_DATA = [
        'tomorrow 00:00' => 'very cloudy',
        'tomorrow 01:00' => 'not so cloudy',
        'tomorrow 02:00' => 'not so cloudy',
        'tomorrow 03:00' => 'somewhat cloudy',
        'tomorrow 00:00 +1 day' => 'somewhat sunny', // Note that 00:00 applies to both days
        'tomorrow 01:00 +1 day' => 'very sunny',
        'tomorrow 00:00 + 2 days' => 'somewhat sunny', // Note that 00:00 applies to both days
    ];


    function let(
        ClientInterface $client,
        ProductRepository $productRepository
    ) {
        $this->beConstructedWith($client, $productRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(WeatherForecastDependentProductRecommender::class);
    }

    function it_recommends_products_based_on_weather_forecast(
        $client,
        $productRepository
    ) {
        $placeCode = 'vilnius';

        $place = new Place('vilnius', 'Vilnius', 'Vilniaus m. sav.', 'LT');
        $forecastType = new ForecastType('my-type', 'My type description');
        $forecastTypeCollection = new ForecastTypeCollection([$forecastType]);

        $forecastCreationTime = new DateTimeImmutable();

        $product1 = new Product();
        $product2 = new Product();
        $product3 = new Product();
        $product4 = new Product();
        $product5 = new Product();
        $product6 = new Product();
        $product7 = new Product();

        $forecasts = [];
        foreach (self::FORECAST_DATA as $date => $conditionCode) {
            $forecasts[] = new Forecast(
                $place,
                $forecastType,
                $forecastCreationTime,
                new DateTimeImmutable($date),
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                $conditionCode
            );
        }

        $forecastCollection = new ForecastCollection($forecasts);

        $client->getPlace($placeCode)->shouldBeCalled()
            ->willReturn($place);
        $client->getForecastTypes($place)->shouldBeCalled()
            ->willReturn($forecastTypeCollection);
        $client->getForecasts($place, $forecastType)->shouldBeCalled()
            ->willReturn($forecastCollection);

        $productRepository->findRandomByWeatherConditionCode('not so cloudy', 3)
            ->shouldBeCalled()
            ->willReturn([$product1, $product2, $product3]);

        $productRepository->findRandomByWeatherConditionCode('somewhat sunny', 3)
            ->shouldBeCalledTimes(2)
            ->willReturn([$product4, $product5], [$product2, $product6, $product7]);

        $recommendations = $this->recommend($placeCode, 3, 3);

        $recommendations->shouldHaveType(LocationAwareRecommendations::class);
        $recommendations->getLocationName()
            ->shouldReturn('Vilnius');

        $actualRecommendations = $recommendations->getRecommendations();
        $actualRecommendations->shouldHaveCount(3);

        $firstRecommendation = $actualRecommendations->first();
        $firstRecommendation->getWeatherConditionCode()
            ->shouldReturn('not so cloudy');
        $firstRecommendation->getProducts()
            ->shouldReturn([$product1, $product2, $product3]);

        $secondRecommendation = $actualRecommendations[1];
        $secondRecommendation->getWeatherConditionCode()
            ->shouldReturn('somewhat sunny');
        $secondRecommendation->getProducts()
            ->shouldReturn([$product4, $product5]);

        $thirdRecommendation = $actualRecommendations[2];
        $thirdRecommendation->getWeatherConditionCode()
            ->shouldReturn('somewhat sunny');
        $thirdRecommendation->getProducts()
            ->shouldReturn([$product2, $product6, $product7]);
    }
}
