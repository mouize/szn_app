<?php

namespace App\EventListener;

use App\Entity\Product;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use App\Document\ProductView;
use App\Repository\ODM\ProductViewRepository;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;

class SynchronizeProductViewSubscriber implements EventSubscriberInterface
{
    public function __construct(private DocumentManager $dm,)
    {
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

        if (null === ($productView = $this->dm
                ->getRepository(ProductView::class)
                ->findOneBy(['productId' => $entity->getId()]))
        ) {
            $productView = new ProductView();
            $productView->setProductId($entity->getId());
        }

        $productView->setName($entity->getName());
        $productView->setPhotoUrl($entity->getPhotoUrl());

        $this->dm
            ->getRepository(ProductView::class)
            ->save($productView, true);
    }
}