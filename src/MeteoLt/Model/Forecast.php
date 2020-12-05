<?php
declare(strict_types=1);

namespace App\MeteoLt\Model;

use DateTimeImmutable;

class Forecast
{
    public const CONDITION_CODE_CLEAR = 'clear';
    public const CONDITION_CODE_ISOLATED_CLOUDS = 'isoloated-clouds';
    public const CONDITION_CODE_SCATTERED_CLOUDS = 'scattered-clouds';
    public const CONDITION_CODE_OVERCAST = 'overcast';
    public const CONDITION_CODE_LIGHT_RAIN = 'light-rain';
    public const CONDITION_CODE_MODERATE_RAIN = 'moderate-rain';
    public const CONDITION_CODE_HEAVY_RAIN = 'heavy-rain';
    public const CONDITION_CODE_SLEET = 'sleet';
    public const CONDITION_CODE_LIGHT_SNOW = 'light-snow';
    public const CONDITION_CODE_MODERATE_SNOW = 'moderate-snow';
    public const CONDITION_CODE_HEAVY_SNOW = 'heavy-snow';
    public const CONDITION_CODE_FOG = 'fog';
    public const CONDITION_CODE_UNKNOWN = 'na';

    private Place $place;
    private ForecastType $forecastType;
    private DateTimeImmutable $forecastCreationTime;
    private DateTimeImmutable $forecastTime;
    private float $airTemperature;
    private float $windSpeed;
    private float $windGust;
    private float $windDirection;
    private float $cloudCover;
    private float $seaLevelPressure;
    private float $relativeHumidity;
    private float $totalPrecipitation;
    private string $conditionCode;

    public function __construct(
        Place $place,
        ForecastType $forecastType,
        DateTimeImmutable $forecastCreationTime,
        DateTimeImmutable $forecastTime,
        float $airTemperature,
        float $windSpeed,
        float $windGust,
        float $windDirection,
        float $cloudCover,
        float $seaLevelPressure,
        float $relativeHumidity,
        float $totalPrecipitation,
        string $conditionCode
    ) {
        $this->place = $place;
        $this->forecastType = $forecastType;
        $this->forecastCreationTime = $forecastCreationTime;
        $this->forecastTime = $forecastTime;
        $this->airTemperature = $airTemperature;
        $this->windSpeed = $windSpeed;
        $this->windGust = $windGust;
        $this->windDirection = $windDirection;
        $this->cloudCover = $cloudCover;
        $this->seaLevelPressure = $seaLevelPressure;
        $this->relativeHumidity = $relativeHumidity;
        $this->totalPrecipitation = $totalPrecipitation;
        $this->conditionCode = $conditionCode;
    }

    public function getPlace(): Place
    {
        return $this->place;
    }

    public function getForecastType(): ForecastType
    {
        return $this->forecastType;
    }

    public function getForecastCreationTime(): DateTimeImmutable
    {
        return $this->forecastCreationTime;
    }

    public function getForecastTime(): DateTimeImmutable
    {
        return $this->forecastTime;
    }

    public function getAirTemperature(): float
    {
        return $this->airTemperature;
    }

    public function getWindSpeed(): float
    {
        return $this->windSpeed;
    }

    public function getWindGust(): float
    {
        return $this->windGust;
    }

    public function getWindDirection(): float
    {
        return $this->windDirection;
    }

    public function getCloudCover(): float
    {
        return $this->cloudCover;
    }

    public function getSeaLevelPressure(): float
    {
        return $this->seaLevelPressure;
    }

    public function getRelativeHumidity(): float
    {
        return $this->relativeHumidity;
    }

    public function getTotalPrecipitation(): float
    {
        return $this->totalPrecipitation;
    }

    public function getConditionCode(): string
    {
        return $this->conditionCode;
    }
}
