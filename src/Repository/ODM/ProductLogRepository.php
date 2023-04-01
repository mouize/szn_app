<?php

namespace App\Repository\ODM;

use App\Document\ProductLog;
use Doctrine\ODM\MongoDB\Repository\DocumentRepository;

class ProductLogRepository extends DocumentRepository
{
    public function save(ProductLog $productLog, bool $flush = false)
    {
        $this->dm->persist($productLog);

        if ($flush) {
            $this->dm->flush();
        }
    }

    public function search(
        ?string $storeName = null,
        ?float $storeLatitude = null,
        ?float $storeLongitude = null,
        ?int $storeRadius = null,
        int $page = 0,
        int $limit = 10,
    ): array {
        $queryBuilder = $this->createQueryBuilder()
            ->field('stores')->exists(true)
            ->hydrate(false)
            ->select('id', 'name', 'photoUrl', 'stores')
            ->distinct('id')
            ->sort('id', 'asc');

        if ($storeName) {
            $queryBuilder->field('stores.name')->equals($storeName);
        }

        if ($storeLatitude && $storeLongitude && $storeRadius) {
            $queryBuilder->addAnd(
                $queryBuilder->expr()->geoNear(
                    $storeLongitude,
                    $storeLatitude
                )
                    ->spherical(true)
                    ->distanceMultiplier(6371)
                    ->maxDistance($storeRadius / 1000)
                    ->distanceField('distance')
            );
        }

        $paginator = new Paginator($queryBuilder);
        $paginator
            ->setCurrentPageNumber($page)
            ->setItemCountPerPage($limit);

        $result = $paginator->getCurrentPageResults();

        return $result;
    }
}
