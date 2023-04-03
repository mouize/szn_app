<?php

namespace App\UseCase\Command;

use App\Service\CQSBus\Command;

class CreateShopCommand implements Command
{
    public function __construct(
        public string $name,
        public float $latitude,
        public float $longitude,
        public string $address,
        public string $manager
    ) {
    }
}
