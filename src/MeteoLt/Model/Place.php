<?php
declare(strict_types=1);

namespace App\MeteoLt\Model;

class Place
{
    private string $code;
    private string $name;
    private string $administrativeDivision;
    private string $countryCode;
    private ?string $countryName;
    private ?float $latitude;
    private ?float $longitude;

    public function __construct(
        string $code,
        string $name,
        string $administrativeDivision,
        string $countryCode,
        ?string $countryName = null,
        ?float $latitude = null,
        ?float $longitude = null
    ) {
        $this->code = $code;
        $this->name = $name;
        $this->administrativeDivision = $administrativeDivision;
        $this->countryCode = $countryCode;
        $this->countryName = $countryName;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAdministrativeDivision(): string
    {
        return $this->administrativeDivision;
    }

    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    public function getCountryName(): ?string
    {
        return $this->countryName;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }
}
