<?php

namespace App\UseCase\Command;

use App\Entity\Product;
use App\Service\CQSBus\CommandHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreateProductCommandHandler implements CommandHandler
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function __invoke(CreateProductCommand $command): void
    {
        $product = new Product();
        $product->setName($command->name);
        $product->setPhotoUrl($command->photoUrl);

        $this->em->getRepository(Product::class)->save($product, true);
    }
}
