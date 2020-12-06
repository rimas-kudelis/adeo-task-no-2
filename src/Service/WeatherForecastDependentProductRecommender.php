<?php
declare(strict_types=1);

namespace App\Service;

use App\Exception\NoForecastsFoundForDateException;
use App\MeteoLt\Client\ClientInterface;
use App\MeteoLt\Collection\ForecastCollection;
use App\MeteoLt\Model\Forecast;
use App\MeteoLt\Model\ForecastType;
use App\MeteoLt\Model\Place;
use App\Collection\RecommendationCollection;
use App\Model\LocationAwareRecommendations;
use App\Model\Recommendation;
use App\Repository\ProductRepository;
use DateTimeImmutable;
use DateTimeInterface;
use InvalidArgumentException;

class WeatherForecastDependentProductRecommender implements DailyLocationDependentProductRecommenderInterface
{
    private ClientInterface $client;
    private ProductRepository $productRepository;
    private ?string $useForecastType;

    public function __construct(
        ClientInterface $client,
        ProductRepository $productRepository,
        ?string $useForecastType = null
    )
    {
        $this->client = $client;
        $this->productRepository = $productRepository;
        $this->useForecastType = $useForecastType;
    }

    public function recommend(string $placeCode, int $days = 3, int $productsPerDay = 3): LocationAwareRecommendations
    {
        $place = $this->client->getPlace($placeCode);
        $forecastType = $this->getForecastType($place);
        $forecasts = $this->client->getForecasts($place, $forecastType);

        $recommendations = $this->getRecommendations($forecasts, $days, $productsPerDay);
        return new LocationAwareRecommendations($place->getName(), $recommendations, 'Weather forecast from meteo.lt was used by this service.');
    }

    protected function getRecommendations(ForecastCollection $forecasts, int $days, int $productsPerDay): RecommendationCollection
    {
        if ($days < 1) {
            throw new InvalidArgumentException(sprintf('%s: $days must be a positive integer, got %d.', __METHOD__, $days));
        }

        if ($productsPerDay < 1) {
            throw new InvalidArgumentException(sprintf('%s: $productsPerDay must be a positive integer, got %d.', __METHOD__, $productsPerDay));
        }

        // Use server timezone here
        $dayEnd = new DateTimeImmutable('tomorrow 00:00');

        $recommendations = new RecommendationCollection();
        for ($day = 1; $day <= $days; $day++) {
            $dayStart = $dayEnd;
            $dayEnd = $dayStart->modify('+1 day');

            try {
                $dominantWeatherConditionCode = $this->getDominantWeatherConditionCode($forecasts, $dayStart, $dayEnd);
            } catch (NoForecastsFoundForDateException $e) {
                // No further forecasts, most likely
                break;
            }

            if (null !== $dominantWeatherConditionCode) {
                $products = $this->productRepository->findRandomByWeatherConditionCode(
                    $dominantWeatherConditionCode,
                    $productsPerDay
                );
            } else {
                $products = [];
            }

            $recommendations->add(new Recommendation(
                $dominantWeatherConditionCode,
                $dayStart,
                $products
            ));
        }

        return $recommendations;
    }

    protected function getForecastType(Place $place): ForecastType
    {
        return null !== $this->useForecastType
            ? new ForecastType($this->useForecastType, 'Default forecast type')
            : $this->client->getForecastTypes($place)->first();
    }

    protected function getDominantWeatherConditionCode(
        ForecastCollection $forecasts,
        DateTimeInterface $dateFrom,
        DateTimeInterface $dateTo
    ): ?string {
        $dateFromTimestamp = $dateFrom->getTimestamp();
        $dateToTimestamp = $dateTo->getTimestamp();

        $relevantForecasts = $forecasts->filter(function(Forecast $forecast) use ($dateFromTimestamp, $dateToTimestamp) {
            $forecastTimestamp = $forecast->getForecastTime()->getTimestamp();
            return $forecastTimestamp >= $dateFromTimestamp && $forecastTimestamp <= $dateToTimestamp;
        });

        if (1 > count($relevantForecasts)) {
            throw new NoForecastsFoundForDateException();
        }

        $conditions = [];
        /** @var Forecast $forecast */
        foreach ($relevantForecasts as $forecast) {
            $conditionCode = $forecast->getConditionCode();
            if (Forecast::CONDITION_CODE_UNKNOWN === $conditionCode) {
                continue;
            }

            if (isset($conditions[$conditionCode])) {
                $conditions[$conditionCode]++;
            } else {
                $conditions[$conditionCode] = 1;
            }
        }

        arsort($conditions);

        // Avoid returning unknown if possible
        if (Forecast::CONDITION_CODE_UNKNOWN === array_key_first($conditions)) {
            array_shift($conditions);
        }

        return array_key_first($conditions);
    }
}
