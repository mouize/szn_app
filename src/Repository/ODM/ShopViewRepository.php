<?php

namespace App\Repository\ODM;

use App\Document\ShopView;
use Doctrine\ODM\MongoDB\Repository\DocumentRepository;

class ShopViewRepository extends DocumentRepository
{
    public function save(ShopView $shopView, bool $flush = false)
    {
        $this->dm->persist($shopView);

        if ($flush) {
            $this->dm->flush();
        }
    }
    
    public function search(
        ?string $name = null,
        ?float $latitude = null,
        ?float $longitude = null,
        ?int $distance = null,
        int $offset = 0,
        int $limit = 10,
    ) {
        $qb = $this->createQueryBuilder()
            ->hydrate(false);

        if ($name) {
            $qb->field('name')->equals($name);
        }

        if ($latitude && $longitude && $distance) {
            $qb->field('location')->geoWithinCenterSphere(
                x: $longitude,
                y: $latitude,
                radius: (float)$distance / 6371
            );
        }

        return $qb
            ->limit($limit)
            ->skip($offset)
            ->getQuery()
            ->execute()
            ->toArray();
    }
}