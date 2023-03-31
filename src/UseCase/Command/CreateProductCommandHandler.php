<?php

namespace App\UseCase\Command;

use App\Entity\Product;
use App\Repository\ORM\ProductRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreateProductCommandHandler
{
    public function __construct(private readonly ProductRepository $productRepository)
    {
    }

    public function __invoke(CreateProductCommand $command): void
    {
        $product = new Product();
        $product->setName($command->name);
        $product->setPhotoUrl($command->photoUrl);

        $this->productRepository->save($product);
    }
}
