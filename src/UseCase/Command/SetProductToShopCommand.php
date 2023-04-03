<?php

namespace App\UseCase\Command;

use App\Service\CQSBus\Command;

class SetProductToShopCommand implements Command
{
    public function __construct(public int $shopId, public int $productId, public int $quantity)
    {
    }
}
