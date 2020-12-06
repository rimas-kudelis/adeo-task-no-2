<?php
declare(strict_types=1);

namespace App\Service;

use App\MeteoLt\Client\ClientInterface;
use App\MeteoLt\Collection\ForecastCollection;
use App\MeteoLt\Collection\ForecastTypeCollection;
use App\MeteoLt\Collection\PlaceCollection;
use App\MeteoLt\Model\ForecastType;
use App\MeteoLt\Model\Place;
use Closure;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Contracts\Cache\ItemInterface;

class CachingMeteoLtClient implements ClientInterface
{
    protected const CACHE_KEY_PLACES = 'meteo.lt|places';
    protected const CACHE_KEY_PLACE = 'meteo.lt|place|{place-code}';
    protected const CACHE_KEY_TEMPLATE_FORECAST_TYPES = 'meteo.lt|forecast-types|{place-code}';
    protected const CACHE_KEY_TEMPLATE_FORECASTS = 'meteo.lt|forecasts|{place-code}|{forecast-type}';
    protected const DEFAULT_CACHE_DURATION = 300; // 5 minutes

    private ClientInterface $client;
    private AdapterInterface $cache;
    private int $cacheDuration;

    public function __construct(ClientInterface $client, AdapterInterface $cache, ?int $cacheDuration = null)
    {
        $this->client = $client;
        $this->cache = $cache;
        $this->cacheDuration = $cacheDuration ?? self::DEFAULT_CACHE_DURATION;
    }

    public function getPlaces(): PlaceCollection
    {
        return $this->getCached(
            self::CACHE_KEY_PLACES,
            [],
            function () {
                return $this->client->getPlaces();
            }
        );
    }

    public function getPlace(string $code): Place
    {
        return $this->getCached(
            self::CACHE_KEY_PLACE,
            ['place-code' => $code],
            function () use ($code) {
                return $this->client->getPlace($code);
            }
        );
    }

    public function getForecastTypes(Place $place): ForecastTypeCollection
    {
        return $this->getCached(
            self::CACHE_KEY_TEMPLATE_FORECAST_TYPES,
            ['place-code' => $place->getCode()],
            function () use ($place) {
                return $this->client->getForecastTypes($place);
            }
        );
    }

    public function getForecasts(Place $place, ForecastType $type): ForecastCollection
    {
        return $this->getCached(
            self::CACHE_KEY_TEMPLATE_FORECASTS,
            ['place-code' => $place->getCode(), 'forecast-type' => $type->getType()],
            function () use ($place, $type) {
                return $this->client->getForecasts($place, $type);
            }
        );
    }

    protected function getCached(string $cacheKeyTemplate, array $cacheKeyReplacements, Closure $valueGetter)
    {
        $cacheKey = $this->getCacheKey($cacheKeyTemplate, $cacheKeyReplacements);

        return $this->cache->get(
            $cacheKey,
            function (ItemInterface $item) use ($valueGetter) {
                $item->expiresAfter($this->cacheDuration);

                return $valueGetter();
            }
        );
    }

    protected function getCacheKey(string $template, array $replacements): string
    {
        $replacementKeys = array_map(fn(string $key) => '{' . $key . '}', array_keys($replacements));
        $replacementValues = array_values($replacements);

        return str_replace($replacementKeys, $replacementValues, $template);
    }
}
