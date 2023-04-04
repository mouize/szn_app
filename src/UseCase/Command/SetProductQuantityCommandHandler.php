<?php

namespace App\UseCase\Command;

use App\Entity\Product;
use App\Entity\Shop;
use App\Entity\Stock;
use App\Service\CQSBus\CommandHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class SetProductQuantityCommandHandler implements CommandHandler
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function __invoke(SetProductQuantityCommand $command)
    {
        $shop = $this->em->getRepository(Shop::class)->find($command->shopId);
        $product = $this->em->getRepository(Product::class)->find($command->productId);

        $stock = $this->em->getRepository(Stock::class)->findOneBy(['shop' => $shop, 'product' => $product]);
        if (null === $stock) {
            $stock = new Stock();
            $stock->setShop($shop);
            $stock->setProduct($product);
        }
        $stock->setQuantity($command->quantity);

        $this->em->getRepository(Stock::class)->save($stock, true);
    }
}
