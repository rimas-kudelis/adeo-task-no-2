<?php
declare(strict_types=1);

namespace App\Model;

use App\Collection\RecommendationCollection;

class LocationAwareRecommendations
{
    private string $locationName;
    private RecommendationCollection $recommendations;
    private ?string $notes;

    public function __construct(string $locationName, RecommendationCollection $recommendations, ?string $notes = null)
    {
        $this->locationName = $locationName;
        $this->recommendations = $recommendations;
        $this->notes = $notes;
    }

    public function getLocationName(): string
    {
        return $this->locationName;
    }

    public function getRecommendations(): RecommendationCollection
    {
        return $this->recommendations;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }
}
