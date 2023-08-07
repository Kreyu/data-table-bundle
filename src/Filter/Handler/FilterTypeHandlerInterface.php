<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter\Handler;

use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Kreyu\Bundle\DataTableBundle\Filter\FilterInterface;
use Kreyu\Bundle\DataTableBundle\Filter\Type\FilterTypeInterface;
use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryInterface;

/**
 * @psalm-template TQuery of ProxyQueryInterface
 */
interface FilterTypeHandlerInterface
{
    /**
     * @psalm-param TQuery $query
     */
    public function handle(ProxyQueryInterface $query, FilterData $data, FilterInterface $filter): void;

    /**
     * @return iterable<class-string<FilterTypeInterface>>
     */
    public static function getHandledTypes(): iterable;
}
