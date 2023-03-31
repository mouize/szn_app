<?php

namespace App\UseCase\Command;

use App\Entity\Shop;
use App\Repository\ORM\ShopRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreateShopCommandHandler
{
    public function __construct(private readonly ShopRepository $shopRepository)
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

        $this->shopRepository->save($shop);
    }
}
