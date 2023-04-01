<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use App\Repository\ODM\ProductLogRepository;

/**
 * @MongoDB\Document(repositoryClass=ProductLogRepository::class)
 */
class ProductLog
{
    /**
     * @MongoDB\Id
     */
    private $id;

    /**
     * @MongoDB\Field(type="int")
     */
    private $productId;

    /**
     * @MongoDB\Field(type="string")
     */
    private $name;

    /**
     * @MongoDB\Field(type="string")
     */
    private $photoUrl;

    /**
     * @MongoDB\Field(type="collection")
     */
    private $shops = [];

    public function getId(): int
    {
        return $this->id;
    }

    public function getProductId(): int
    {
        return $this->productId;
    }

    public function setProductId(int $productId)
    {
        $this->productId = $productId;
    }


    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getPhotoUrl(): ?string
    {
        return $this->photoUrl;
    }

    public function setPhotoUrl(string $photoUrl): void
    {
        $this->photoUrl = $photoUrl;
    }

    public function getShops(): ?array
    {
        return $this->shops;
    }

    public function setShops(array $shops): void
    {
        $this->shops = $shops;
    }
}
