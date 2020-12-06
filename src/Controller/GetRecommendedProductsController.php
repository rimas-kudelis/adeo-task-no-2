<?php
declare(strict_types=1);

namespace App\Controller;

use App\Service\WeatherForecastDependentProductRecommender;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class GetRecommendedProductsController extends AbstractController
{
    private WeatherForecastDependentProductRecommender $recommender;
    private NormalizerInterface $normalizer;

    public function __construct(WeatherForecastDependentProductRecommender $recommender, NormalizerInterface $normalizer)
    {
        $this->recommender = $recommender;
        $this->normalizer = $normalizer;
    }

    public function __invoke(string $locationCode): Response
    {
        return new JsonResponse($this->normalizer->normalize(
            $this->recommender->recommend($locationCode, 3, 3),
            'json',
            [AbstractObjectNormalizer::IGNORED_ATTRIBUTES => ['weatherConditions']]
        ));
    }
}
