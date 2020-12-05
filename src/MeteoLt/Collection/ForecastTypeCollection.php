<?php
declare(strict_types=1);

namespace App\MeteoLt\Collection;

use App\MeteoLt\Model\ForecastType;
use Ramsey\Collection\AbstractCollection;

class ForecastTypeCollection extends AbstractCollection
{
    public function getType(): string
    {
        return ForecastType::class;
    }
}
