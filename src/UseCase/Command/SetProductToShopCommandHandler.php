<?php

namespace App\UseCase\Command;

use App\Entity\Stock;
use App\Repository\ORM\ProductRepository;
use App\Repository\ORM\ShopRepository;
use App\Repository\ORM\StockRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class SetProductToShopCommandHandler
{
    public function __construct(
        private ShopRepository $shopRepository,
        private ProductRepository $productRepository,
        private StockRepository $stockRepository,
    ) {
    }

    public function __invoke(SetProductToShopCommand $command)
    {
        $shop = $this->shopRepository->find($command->shopId);
        $product = $this->productRepository->find($command->productId);

        $stock = $this->stockRepository->findOneBy(['shop' => $shop, 'product' => $product]);
        if (null === $stock) {
            $stock = new Stock();
            $stock->setShop($shop);
            $stock->setProduct($product);
        }

        $stock->setQuantity($command->quantity);

        $this->stockRepository->save($stock, true);
    }
}
