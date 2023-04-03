<?php

namespace App\UseCase\Query;

use App\Service\CQSBus\Query;

class SearchProductQuery implements Query
{
    public function __construct(
        public ?string $name = null,
        public array $shops = [],
        public int $page = 0,
        public int $limit = 10,
    ) {
    }
}