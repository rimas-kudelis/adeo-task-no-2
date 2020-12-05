<?php
declare(strict_types=1);

namespace App\MeteoLt\Collection;

use App\MeteoLt\Model\Forecast;
use Ramsey\Collection\AbstractCollection;

class ForecastCollection extends AbstractCollection
{
    public function getType(): string
    {
        return Forecast::class;
    }
}
