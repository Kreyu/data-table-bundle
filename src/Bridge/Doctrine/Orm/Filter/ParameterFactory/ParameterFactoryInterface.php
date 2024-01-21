<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\ParameterFactory;

use Doctrine\ORM\Query\Parameter;
use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Kreyu\Bundle\DataTableBundle\Filter\FilterInterface;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Query\DoctrineOrmProxyQueryInterface;

interface ParameterFactoryInterface
{
    /**
     * @return array<Parameter>
     */
    public function create(DoctrineOrmProxyQueryInterface $query, FilterData $data, FilterInterface $filter): array;
}
