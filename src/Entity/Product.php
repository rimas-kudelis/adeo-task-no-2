<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table()
 */
class Product
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue()
     */
    protected int $id;

    /**
     * @ORM\Column(type="string")
     */
    protected string $sku;

    /**
     * @ORM\Column(type="string")
     */
    protected string $name;

    /**
     * @ORM\Column(type="integer")
     */
    protected int $price;

    /**
     * @ORM\ManyToMany(targetEntity="WeatherCondition", inversedBy="products", cascade={"persist"})
     */
    protected Collection $weatherConditions;

    public function __construct()
    {
        $this->weatherConditions = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getSku(): string
    {
        return $this->sku;
    }

    public function setSku(string $sku): self
    {
        $this->sku = $sku;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPrice(): int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getWeatherConditions(): Collection
    {
        return $this->weatherConditions;
    }

    public function addWeatherCondition(WeatherCondition $weatherCondition): self
    {
        if (!$this->weatherConditions->contains($weatherCondition)) {
            $this->weatherConditions->add($weatherCondition);
        }

        return $this;
    }

    public function removeWeatherCondition(WeatherCondition $weatherCondition): self
    {
        if ($this->weatherConditions->contains($weatherCondition)) {
            $this->weatherConditions->removeElement($weatherCondition);
        }

        return $this;
    }

    public function hasWeatherCondition(WeatherCondition $weatherCondition): bool
    {
        return $this->weatherConditions->contains($weatherCondition);
    }
}
