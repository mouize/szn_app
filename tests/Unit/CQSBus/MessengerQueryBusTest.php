<?php

namespace App\Tests\Unit\CQSBus;

use App\Service\CQSBus\MessengerQueryBus;
use App\Service\CQSBus\QueryBus;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\MessageBusInterface;

class MessengerQueryBusTest extends TestCase
{
    public function testWHENNewInstanceTHENHasInterface(): void
    {
        $messageBus = $this->createMock(MessageBusInterface::class);
        $commandBus = new MessengerQueryBus($messageBus);
        $this->assertInstanceOf(QueryBus::class, $commandBus);
    }

    // Testing the handle method is too complicated, maybe one day :p
}
