<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type;

use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Query\ProxyQueryInterface as DoctrineOrmProxyQueryInterface;
use Kreyu\Bundle\DataTableBundle\Exception\UnexpectedTypeException;
use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Kreyu\Bundle\DataTableBundle\Filter\FilterInterface;
use Kreyu\Bundle\DataTableBundle\Filter\Type\AbstractType as BaseAbstractType;
use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryInterface;

abstract class AbstractType extends BaseAbstractType
{
    abstract protected function filter(DoctrineOrmProxyQueryInterface $query, FilterData $data, FilterInterface $filter): void;

    public function apply(ProxyQueryInterface $query, FilterData $data, FilterInterface $filter): void
    {
        if (!is_a($query, DoctrineOrmProxyQueryInterface::class)) {
            throw new UnexpectedTypeException($query, DoctrineOrmProxyQueryInterface::class);
        }

        $this->filter($query, $data, $filter);
    }

    protected function generateUniqueParameterName(ProxyQueryInterface $query, FilterInterface $filter): string
    {
        return $filter->getFormName().'_'.$query->getUniqueParameterId();
    }
}
