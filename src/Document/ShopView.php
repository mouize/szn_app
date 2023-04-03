<?php

namespace App\Document;


use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use App\Repository\ODM\ShopViewRepository;

/**
 * @MongoDB\Document(repositoryClass=ShopViewRepository::class)
 */
class ShopView
{
    /**
     * @MongoDB\Id
     */
    private $id;

    /**
     * @MongoDB\Field(type="int")
     */
    private int $shopId;

    /**
     * @MongoDB\Field(type="string")
     */
    private string $name;

    /**
     * @MongoDB\Field(type="collection")
     */
    private array $location;

    /**
     * @MongoDB\Field(type="string")
     */
    private string $address;

    /**
     * @MongoDB\Field(type="string")
     */
    private string $manager;

    public function getId(): int
    {
        return $this->id;
    }

    public function getShopId(): int
    {
        return $this->shopId;
    }

    public function setShopId(int $shopId): ShopView
    {
        $this->shopId = $shopId;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getLocation(): array
    {
        return $this->location;
    }

    public function setLocation(array $location): static
    {
        $this->location = $location;

        return $this;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getManager(): string
    {
        return $this->manager;
    }

    public function setManager(string $manager): static
    {
        $this->manager = $manager;

        return $this;
    }
}