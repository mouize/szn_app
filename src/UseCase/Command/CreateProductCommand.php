<?php

namespace App\UseCase\Command;

class CreateProductCommand
{
    public function __construct(
        public string $name,
        public ?string $photoUrl,
    ) {
    }
}
