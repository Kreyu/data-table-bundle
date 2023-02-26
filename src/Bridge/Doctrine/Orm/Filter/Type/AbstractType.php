<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type;

use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Query\DoctrineOrmProxyQuery;
use Kreyu\Bundle\DataTableBundle\Filter\FilterInterface;
use Kreyu\Bundle\DataTableBundle\Filter\Type\AbstractType as BaseAbstractType;
use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryInterface;

abstract class AbstractType extends BaseAbstractType
{
    /**
     * @param DoctrineOrmProxyQuery $query
     */
    public function getUniqueParameterName(ProxyQueryInterface $query, FilterInterface $filter): string
    {
        return $filter->getFormName().'_'.$query->getUniqueParameterId();
    }

    /**
     * @param DoctrineOrmProxyQuery $query
     */
    protected function getFilterQueryPath(ProxyQueryInterface $query, FilterInterface $filter): string
    {
        $rootAlias = current($query->getRootAliases());

        $queryPath = $filter->getQueryPath();

        if ($rootAlias && !str_contains($queryPath, '.') && $filter->getOption('auto_alias_resolving')) {
            $queryPath = $rootAlias.'.'.$queryPath;
        }

        return $queryPath;
    }
}
