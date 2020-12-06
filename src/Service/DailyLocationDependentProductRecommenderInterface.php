<?php
declare(strict_types=1);

namespace App\Service;

use App\Model\LocationAwareRecommendations;

interface DailyLocationDependentProductRecommenderInterface
{
    public function recommend(string $placeCode, int $days = 3, int $productsPerDay = 3): LocationAwareRecommendations;
}
