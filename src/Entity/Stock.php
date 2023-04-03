<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ORM\StockRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Stock
{
    /**
     * @ORM\Id()
     *
     * @ORM\GeneratedValue()
     *
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Shop", inversedBy="stocks")
     *
     * @ORM\JoinColumn(nullable=false)
     */
    private Shop $shop;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Product", inversedBy="stocks")
     *
     * @ORM\JoinColumn(nullable=false)
     */
    private Product $product;

    /**
     * @ORM\Column(type="integer")
     */
    private int $quantity;

    public function getId(): int
    {
        return $this->id;
    }

    public function getShop(): Shop
    {
        return $this->shop;
    }

    public function setShop(Shop $shop): static
    {
        $this->shop = $shop;

        return $this;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function setProduct(Product $product): static
    {
        $this->product = $product;

        return $this;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }
}
