<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use App\Repository\ODM\ProductViewRepository;

/**
 * @MongoDB\Document(repositoryClass=ProductViewRepository::class)
 */
class ProductView
{
    /**
     * @MongoDB\Id
     */
    private $id;

    /**
     * @MongoDB\Field(type="int")
     */
    private int $productId;

    /**
     * @MongoDB\Field(type="string")
     */
    private string $name;

    /**
     * @MongoDB\Field(type="string")
     */
    private string $photoUrl;

    /**
     * @MongoDB\Field(type="collection")
     */
    private array $shops = [];

    public function getId(): int
    {
        return $this->id;
    }

    public function getProductId(): int
    {
        return $this->productId;
    }

    public function setProductId(int $productId): static
    {
        $this->productId = $productId;

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

    public function getPhotoUrl(): ?string
    {
        return $this->photoUrl;
    }

    public function setPhotoUrl(?string $photoUrl): static
    {
        $this->photoUrl = $photoUrl;

        return $this;
    }

    public function getShops(): ?array
    {
        return $this->shops;
    }

    public function setShops(array $shops): static
    {
        $this->shops = $shops;

        return $this;
    }
}
