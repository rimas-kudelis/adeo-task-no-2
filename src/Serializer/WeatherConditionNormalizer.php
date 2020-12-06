<?php
declare(strict_types=1);

namespace App\Serializer;

use App\Entity\WeatherCondition;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class WeatherConditionNormalizer implements NormalizerInterface, CacheableSupportsMethodInterface
{
    public function normalize($object, ?string $format = null, array $context = [])
    {
        if (!$this->supportsNormalization($object, $format)) {
            throw new InvalidArgumentException();
        }

        /** @var WeatherCondition $object */
        return $object->getCode();
    }

    public function supportsNormalization($data, ?string $format = null)
    {
        return $data instanceof WeatherCondition;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}
