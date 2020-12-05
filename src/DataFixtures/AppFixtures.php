<?php

namespace App\DataFixtures;

use App\Entity\Product;
use App\Entity\WeatherCondition;
use App\MeteoLt\Model\Forecast;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $weatherConditionCodes = [
            Forecast::CONDITION_CODE_CLEAR,
            Forecast::CONDITION_CODE_ISOLATED_CLOUDS,
            Forecast::CONDITION_CODE_FOG,
            Forecast::CONDITION_CODE_HEAVY_RAIN,
            Forecast::CONDITION_CODE_MODERATE_RAIN,
            Forecast::CONDITION_CODE_LIGHT_RAIN,
            Forecast::CONDITION_CODE_HEAVY_SNOW,
            Forecast::CONDITION_CODE_MODERATE_SNOW,
            Forecast::CONDITION_CODE_LIGHT_SNOW,
            Forecast::CONDITION_CODE_SLEET,
            Forecast::CONDITION_CODE_OVERCAST,
            Forecast::CONDITION_CODE_SCATTERED_CLOUDS,
        ];

        $suffixToWeatherConditionMap = [
            'enjoying your sunny days' => [
                Forecast::CONDITION_CODE_CLEAR,
                Forecast::CONDITION_CODE_ISOLATED_CLOUDS,
            ],
            'slipping through foggy mornings' => [
                Forecast::CONDITION_CODE_FOG,
            ],
            'battling rainy weather' => [
                Forecast::CONDITION_CODE_HEAVY_RAIN,
                Forecast::CONDITION_CODE_MODERATE_RAIN,
                Forecast::CONDITION_CODE_LIGHT_RAIN,
            ],
            'surviving snowy winters' => [
                Forecast::CONDITION_CODE_HEAVY_SNOW,
                Forecast::CONDITION_CODE_MODERATE_SNOW,
                Forecast::CONDITION_CODE_LIGHT_SNOW,
                Forecast::CONDITION_CODE_SLEET,
            ],
            'dealing with cloudy skies' => [
                Forecast::CONDITION_CODE_OVERCAST,
                Forecast::CONDITION_CODE_SCATTERED_CLOUDS,
            ]
        ];

        $productAdjectives = [
            'Awesome',
            'Funny',
            'Bright',
            'Cosy',
            'Leather',
            'Wooden',
        ];

        $productTypes = [
            'jacket',
            'suit',
            'trousers',
            'bikini',
            'sunglasses',
            'shovel',
            'bag',
            'backpack',
            'umbrella',

        ];

        $conditions = [];
        foreach ($weatherConditionCodes as $weatherConditionCode) {
            $weatherCondition = (new WeatherCondition())->setCode($weatherConditionCode);
            $manager->persist($weatherCondition);
            $conditions[$weatherConditionCode] = $weatherCondition;
        }

        for ($i = 0; $i < 1000; $i++) {
            $suffix = array_rand($suffixToWeatherConditionMap);
            $weatherConditionCodes = $suffixToWeatherConditionMap[$suffix];
            $prefix = $this->getRandomValue($productAdjectives);
            $productType = $this->getRandomValue($productTypes);

            $product = (new Product())
                ->setSku('PROD' . str_pad((string) $i, '4', '0'))
                ->setName(sprintf(
                    '%s %s for %s',
                    $prefix,
                    $productType,
                    $suffix
                ))
                ->setPrice(random_int(500, 9000))
                ;
            foreach ($weatherConditionCodes as $weatherConditionCode) {
                $product->addWeatherCondition($conditions[$weatherConditionCode]);
            }

            $manager->persist($product);
        }

        $manager->flush();
    }

    protected function getRandomValue(array $array)
    {
        return $array[array_rand($array)];
    }
}
