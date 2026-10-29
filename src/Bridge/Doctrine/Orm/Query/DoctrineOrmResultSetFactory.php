<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Query;

use Doctrine\ORM\Query;
use Doctrine\ORM\Tools\Pagination\OffsetPaginator;
use Doctrine\ORM\Tools\Pagination\Window;
use Doctrine\ORM\Tools\Pagination\WindowPage;
use Kreyu\Bundle\DataTableBundle\Query\ResultSet;
use Kreyu\Bundle\DataTableBundle\Util\RewindableGeneratorIterator;

class DoctrineOrmResultSetFactory implements DoctrineOrmResultSetFactoryInterface
{
    public function create(OffsetPaginator $paginator, Query $query, int $batchSize = 5000): ResultSet
    {
        $firstResult = $query->getFirstResult();
        $maxResults = $query->getMaxResults() ?? 0;

        if ($maxResults > 0) {
            $page = $paginator->paginate($query, new Window($firstResult, $maxResults));

            return new ResultSet(new \ArrayIterator($page->getItems()), $page->count(), $page->getTotalCount());
        }

        $firstBatch = $paginator->paginate($query, new Window($firstResult, $batchSize));
        $totalItemCount = $firstBatch->getTotalCount();

        $items = new RewindableGeneratorIterator(fn () => $this->getBatchedItems($paginator, $query, $firstBatch));

        return new ResultSet($items, $totalItemCount, $totalItemCount);
    }

    /**
     * @param WindowPage<mixed> $firstBatch
     */
    private function getBatchedItems(OffsetPaginator $paginator, Query $query, WindowPage $firstBatch): \Generator
    {
        $batch = $firstBatch;

        while (true) {
            /** @var mixed $item */
            foreach ($batch as $item) {
                yield $item;
            }

            $hasNextPage = $batch->hasNextPage();

            if (!$hasNextPage) {
                return;
            }

            $batch = $paginator->paginate($query, $batch->getNextWindow());
        }
    }
}
