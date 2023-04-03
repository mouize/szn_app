<?php

namespace App\EventListener;

use App\Document\ProductView;
use App\Entity\Stock;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class SynchronizeProductViewStocksSubscriber implements EventSubscriberInterface
{
    private Serializer $serializer;

    public function __construct(private DocumentManager $dm)
    {
        $this->serializer = new Serializer([new ObjectNormalizer()]);
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::postPersist,
            Events::postUpdate,
        ];
    }

    public function postPersist(LifecycleEventArgs $args): void
    {
        $this->synchronize($args);
    }

    public function postUpdate(LifecycleEventArgs $args): void
    {
        $this->synchronize($args);
    }

    private function synchronize(LifecycleEventArgs $args): void
    {
        /**
         * @var Stock $entity
         */
        $entity = $args->getObject();
        if (!$entity instanceof Stock) {
            return;
        }

        $product = $entity->getProduct();
        $shop = $entity->getShop();

        $productView = $this->dm->getRepository(ProductView::class)->findOneBy([
            'productId' => $product->getId()
        ]);
        if (null === $productView) {
            $productView = new ProductView();
            $productView->setProductId($product->getId());
            $productView->setName($product->getName());
            $productView->setPhotoUrl($product->getPhotoUrl());
        }

        $ProductViewShops = $productView->getShops();

        $shopView = $this->serializer->normalize($shop);

        //Would need an appropriate serializer.
        unset($shopView['longitude'], $shopView['latitude'], $shopView['id']);
        $shopView['shopId'] = $shop->getId();
        $shopView['location'] = $shop->getLocation();
        $shopView['quantity'] = $entity->getQuantity();

        //If shops already exists, update it
        if (false !== ($key = array_search($shop->getId(), array_column($ProductViewShops, 'shopId')))) {
            $ProductViewShops[$key] = $shopView;
        } else {
            $ProductViewShops[] = $shopView;
        }

        $productView->setShops($ProductViewShops);

        $this->dm->getRepository(ProductView::class)->save($productView, true);
    }
}