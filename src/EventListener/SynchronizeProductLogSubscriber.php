<?php

namespace App\EventListener;

use App\Entity\Product;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use App\Document\ProductLog;
use App\Repository\ODM\ProductLogRepository;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;

class SynchronizeProductLogSubscriber implements EventSubscriberInterface
{
    private ProductLogRepository $productLogRepository;

    public function __construct(DocumentManager $documentManager,)
    {
        $this->productLogRepository = $documentManager->getRepository(ProductLog::class);
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::postPersist,
        ];
    }

    public function postPersist(LifecycleEventArgs $args): void
    {
        $this->synchronize($args);
    }

    private function synchronize(LifecycleEventArgs $args): void
    {
        /**
         * @var Product $entity
         */
        $entity = $args->getObject();
        if (!$entity instanceof Product) {
            return;
        }

        if (null === ($productLog = $this->productLogRepository->findOneBy(['productId' => $entity->getId()]))) {
            $productLog = new ProductLog();
            $productLog->setProductId($entity->getId());
        }

        $productLog->setName($entity->getName());
        $productLog->setPhotoUrl($entity->getPhotoUrl());

        $this->productLogRepository->save($productLog, true);
    }
}