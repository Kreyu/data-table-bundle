<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter;

use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Query\ProxyQueryInterface;
use Kreyu\Bundle\DataTableBundle\Filter\AbstractFilter as BaseAbstractFilter;
use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Kreyu\Bundle\DataTableBundle\Filter\Operator;
use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryInterface as BaseProxyQueryInterface;

abstract class AbstractFilter extends BaseAbstractFilter
{
    abstract protected function filter(ProxyQueryInterface $query, FilterData $data): void;

    /**
     * @return array<Operator>
     */
    abstract protected function getSupportedOperators(): array;

    public function apply(BaseProxyQueryInterface $query, FilterData $data): void
    {
        if (!$query instanceof ProxyQueryInterface) {
            throw new \InvalidArgumentException();
        }

        if (!in_array($data->getOperator(), $this->getSupportedOperators())) {
            return;
        }

        $this->filter($query, $data);
    }

    protected function generateUniqueParameterName(ProxyQueryInterface $query): string
    {
        return str_replace('.', '_', $this->getName()) . '_' . $query->getUniqueParameterId();
    }
}
