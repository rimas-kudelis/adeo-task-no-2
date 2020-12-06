<?php
declare(strict_types=1);

namespace App\Serializer;

use App\Entity\Product;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class ProductNormalizer implements NormalizerInterface, CacheableSupportsMethodInterface
{
    private ObjectNormalizer $objectNormalizer;

    public function __construct(ObjectNormalizer $objectNormalizer)
    {
        $this->objectNormalizer = $objectNormalizer;
    }

    public function normalize($object, ?string $format = null, array $context = [])
    {
        if (!$this->supportsNormalization($object, $format)) {
            throw new InvalidArgumentException();
        }

        $result = $this->objectNormalizer->normalize($object, $format, $context);
        if (array_key_exists('price', $result)) {
            $result['price'] = $result['price'] / 100;
        }

        return $result;
    }

    public function supportsNormalization($data, ?string $format = null)
    {
        return $data instanceof Product;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}
