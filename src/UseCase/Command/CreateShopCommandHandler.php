<?php

namespace App\UseCase\Command;

use App\Entity\Shop;
use App\Service\CQSBus\CommandHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreateShopCommandHandler implements CommandHandler
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function __invoke(CreateShopCommand $command): void
    {
        $shop = new Shop();
        $shop->setName($command->name);
        $shop->setLatitude($command->latitude);
        $shop->setLongitude($command->longitude);
        $shop->setAddress($command->address);
        $shop->setManager($command->manager);

        $this->em->getRepository(Shop::class)->save($shop);
    }
}
