<?php

namespace App\UseCase\Command;

class SetProductToShopCommand
{
    public function __construct(public int $shopId, public int $productId, public int $quantity)
    {
    }
}
