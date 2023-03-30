<?php

namespace App\Service\CQSBus;

interface CommandBus
{
    public function dispatch(Command $command): void;
}