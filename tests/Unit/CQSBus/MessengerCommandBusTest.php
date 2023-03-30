<?php

namespace App\Tests\Unit\CQSBus;

use App\Service\CQSBus\Command;
use App\Service\CQSBus\CommandBus;
use App\Service\CQSBus\MessengerCommandBus;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

class MessengerCommandBusTest extends TestCase
{
    private MessageBusInterface|MockObject $messageBus;

    public function setUp(): void
    {
        parent::setUp();

        $this->messageBus = $this->createMock(MessageBusInterface::class);
    }

    public function testWHENNewInstanceTHENHasInterface(): void
    {
        $commandBus = new MessengerCommandBus($this->messageBus);
        $this->assertInstanceOf(CommandBus::class, $commandBus);
    }

    public function testWHENDispatchTHENMessageBusHandleIsExpected(): void
    {
        $command = $this->createMock(Command::class);
        $this->messageBus
            ->expects(self::once())
            ->method('dispatch')
            ->willReturn(new Envelope($command)); // must add the willReturn to avoid error on final class.

        $commandBus = new MessengerCommandBus($this->messageBus);
        $commandBus->dispatch($command);
    }
}
