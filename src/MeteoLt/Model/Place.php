<?php
declare(strict_types=1);

namespace App\MeteoLt\Model;

class Place
{
    private string $code;
    private string $name;
    private string $administrativeDivision;
    private string $countryCode;

    public function __construct(string $code, string $name, string $administrativeDivision, string $countryCode)
    {
        $this->code = $code;
        $this->name = $name;
        $this->administrativeDivision = $administrativeDivision;
        $this->countryCode = $countryCode;
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
}
