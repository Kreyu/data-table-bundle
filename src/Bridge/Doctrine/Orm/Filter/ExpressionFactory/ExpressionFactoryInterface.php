<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\ExpressionFactory;

use Doctrine\ORM\Query\Parameter;
use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Kreyu\Bundle\DataTableBundle\Filter\FilterInterface;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Query\DoctrineOrmProxyQueryInterface;

interface ExpressionFactoryInterface
{
    /**
     * @param array<Parameter> $parameters
     */
    public function create(DoctrineOrmProxyQueryInterface $query, FilterData $data, FilterInterface $filter, array $parameters): mixed;
}
