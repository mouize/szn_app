<?php

namespace App\EventListener;

use App\Document\ProductLog;
use App\Entity\Stock;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class SynchronizeProductLogStocksSubscriber implements EventSubscriberInterface
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

        $productLog = $this->dm->getRepository(ProductLog::class)->findOneBy([
            'productId' => $product->getId()
        ]);
        if (null === $productLog) {
            $productLog = new ProductLog();
            $productLog->setProductId($product->getId());
            $productLog->setName($product->getName());
            $productLog->setPhotoUrl($product->getPhotoUrl());
        }

        $shopLogs = $productLog->getShops();

        $shopLog = $this->serializer->normalize($shop);
        $shopLog['quantity'] = $entity->getQuantity();
        //If shops already exists, update it
        if (false !== ($key = array_search($shop->getId(), array_column($shopLogs, 'id')))) {
            $shops[$key] = $shopLog;
        } else {
            $shops[] = $shopLog;
        }

        $productLog->setShops($shops);

        $this->dm->getRepository(ProductLog::class)->save($productLog, true);
    }
}