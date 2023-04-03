<?php

namespace App\EventListener;

use App\Document\ShopView;
use App\Entity\Shop;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;

class SynchronizeShopViewSubscriber implements EventSubscriberInterface
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
         * @var Shop $entity
         */
        $entity = $args->getObject();
        if (!$entity instanceof Shop) {
            return;
        }

        if (null === ($shopView = $this->dm
                ->getRepository(ShopView::class)
                ->findOneBy(['shopId' => $entity->getId()]))
        ) {
            $shopView = new ShopView();
            $shopView->setShopId($entity->getId());
        }

        $shopView->setName($entity->getName());
        $shopView->setLocation([$entity->getLongitude(), $entity->getLatitude()]);
        $shopView->setAddress($entity->getAddress());
        $shopView->setManager($entity->getManager());

        $this->dm->getRepository(ShopView::class)->save($shopView, true);
    }
}