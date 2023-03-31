<?php

namespace App\UseCase\Command;

class CreateShopCommand
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
