<?php

namespace App\UseCase\Query;

use App\Document\ShopView;
use App\Service\CQSBus\QueryHandler;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class SearchShopQueryHandler implements QueryHandler
{
    public function __construct(private DocumentManager $dm)
    {
    }

    public function __invoke(SearchShopQuery $query)
    {
        return $this->dm
            ->getRepository(ShopView::class)
            ->search($query->name, $query->latitude, $query->longitude, $query->distance, $query->page, $query->limit);
    }
}