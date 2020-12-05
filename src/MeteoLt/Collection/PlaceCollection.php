<?php
declare(strict_types=1);

namespace App\MeteoLt\Collection;

use App\MeteoLt\Model\Place;
use Ramsey\Collection\AbstractCollection;

class PlaceCollection extends AbstractCollection
{
    public function getType(): string
    {
        return Place::class;
    }
}
