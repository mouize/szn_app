<?php

namespace App\Service\CQSBus;

interface QueryBus
{
    public function handle(Query $query): mixed;
}