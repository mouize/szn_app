<?php

namespace App\Repository\ODM;

use App\Document\ProductView;
use Doctrine\ODM\MongoDB\Repository\DocumentRepository;

class ProductViewRepository extends DocumentRepository
{
    public function save(ProductView $productView, bool $flush = false)
    {
        $this->dm->persist($productView);

        if ($flush) {
            $this->dm->flush();
        }
    }

    public function search(
        ?string $name = null,
        array $shops = [],
        int $offset = 0,
        int $limit = 10,
    ) {
        $qb = $this->createQueryBuilder()
            ->hydrate(false);

        if ($name) {
            $qb->field('name')->equals($name);
        }

        if ($shops) {
            $shops = array_map('intval', $shops); // Be sure that values are integer
            $qb->field('shops.shopId')->in($shops);
        }

        return $qb
            ->limit($limit)
            ->skip($offset * $limit)
            ->getQuery()
            ->execute()
            ->toArray();
    }
}
