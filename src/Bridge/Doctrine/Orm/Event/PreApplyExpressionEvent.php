<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Event;

use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Kreyu\Bundle\DataTableBundle\Filter\FilterInterface;
use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryInterface;

class PreApplyExpressionEvent extends DoctrineOrmFilterEvent
{
    public function __construct(
        ProxyQueryInterface $query,
        FilterData $data,
        FilterInterface $filter,
        private mixed $expression,
    ) {
        parent::__construct($query, $data, $filter);
    }

    public function getExpression(): mixed
    {
        return $this->expression;
    }

    public function setExpression(mixed $expression): void
    {
        $this->expression = $expression;
    }
}
