<?php
declare(strict_types=1);

namespace App\Collection;

use App\Model\Recommendation;
use Ramsey\Collection\AbstractCollection;

class RecommendationCollection extends AbstractCollection
{
    public function getType(): string
    {
        return Recommendation::class;
    }
}
