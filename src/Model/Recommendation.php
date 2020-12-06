<?php
declare(strict_types=1);

namespace App\Model;

use DateTimeImmutable;
use Symfony\Component\Serializer\Annotation as Serializer;

class Recommendation
{
    protected string $weatherConditionCode;
    protected DateTimeImmutable $date;
    /**
     * @Serializer\MaxDepth(1)
     */
    protected array $products;

    public function __construct(string $weatherConditionCode, DateTimeImmutable $date, array $products)
    {
        $this->weatherConditionCode = $weatherConditionCode;
        $this->date = $date;
        $this->products = $products;
    }

    public function getWeatherConditionCode(): string
    {
        return $this->weatherConditionCode;
    }

    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }

    public function getProducts(): array
    {
        return $this->products;
    }
}
