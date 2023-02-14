<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type;

use Kreyu\Bundle\DataTableBundle\Filter\Type\FilterTypeInterface;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Query\ProxyQueryInterface;
use Kreyu\Bundle\DataTableBundle\Filter\Type\AbstractType as BaseAbstractType;
use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryInterface as BaseProxyQueryInterface;

/**
 * @template-extends FilterTypeInterface<ProxyQueryInterface>
 */
abstract class AbstractType extends BaseAbstractType
{
    public function supports(BaseProxyQueryInterface $query): bool
    {
        return is_a($query, ProxyQueryInterface::class);
    }
}
