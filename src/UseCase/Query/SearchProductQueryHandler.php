<?php

namespace App\UseCase\Query;

use App\Document\ProductView;
use App\Service\CQSBus\QueryHandler;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class SearchProductQueryHandler implements QueryHandler
{
    public function __construct(private DocumentManager $dm)
    {
    }

    public function __invoke(SearchProductQuery $query)
    {
        return $this->dm
            ->getRepository(ProductView::class)
            ->search($query->name, $query->shops, $query->page, $query->limit);
    }
}