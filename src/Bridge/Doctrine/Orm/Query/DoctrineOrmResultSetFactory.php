<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Query;

use Doctrine\ORM\Tools\Pagination\Paginator;
use Kreyu\Bundle\DataTableBundle\Query\ResultSet;
use Kreyu\Bundle\DataTableBundle\Util\RewindableGeneratorIterator;

class DoctrineOrmResultSetFactory implements DoctrineOrmResultSetFactoryInterface
{
    public function create(Paginator $paginator, int $batchSize = 5000): ResultSet
    {
        $items = new RewindableGeneratorIterator(fn () => $this->getPaginatorItems($paginator, $batchSize));

        $currentPageItemCount = $totalItemCount = $paginator->count();

        if ($paginator->getQuery()->getMaxResults() > 0) {
            $items = new \ArrayIterator(iterator_to_array($items));
            $currentPageItemCount = iterator_count($items);
        }

        return new ResultSet($items, $currentPageItemCount, $totalItemCount);
    }

    private function getPaginatorItems(Paginator $paginator, int $batchSize): \Generator
    {
        $query = $paginator->getQuery();

        $firstResult = $query->getFirstResult();
        $maxResults = $limit = $query->getMaxResults();

        if (null === $maxResults || $maxResults > $batchSize) {
            $maxResults = $batchSize;
        }

        $hasItems = true;

        $cursorPosition = 0;

        while ($hasItems && $firstResult < $paginator->count() && (null === $limit || $cursorPosition < $limit)) {
            $hasItems = false;

            $query
                ->setMaxResults($maxResults)
                ->setFirstResult($firstResult);

            foreach ($paginator as $item) {
                yield $item;

                $hasItems = true;

                ++$cursorPosition;
            }

            $firstResult += $cursorPosition;

            $paginator->getQuery()->getEntityManager()->clear();
        }
    }
}
