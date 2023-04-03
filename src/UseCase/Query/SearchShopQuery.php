<?php

namespace App\UseCase\Query;

use App\Service\CQSBus\Query;

class SearchShopQuery implements Query
{
    public function __construct(
        public ?string $name = null,
        public ?float $latitude = null,
        public ?float $longitude = null,
        public ?int $distance = null,
        public int $page = 0,
        public int $limit = 10,
    ) {
    }
}