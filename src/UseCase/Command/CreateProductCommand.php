<?php

namespace App\UseCase\Command;

use App\Service\CQSBus\Command;

class CreateProductCommand implements Command
{
    public function __construct(
        public string $name,
        public ?string $photoUrl,
    ) {
    }
}
