<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Query;

use Doctrine\ORM\QueryBuilder;
use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryInterface as BaseProxyQueryInterface;

/**
 * @mixin QueryBuilder
 */
interface ProxyQueryInterface extends BaseProxyQueryInterface
{
    public function getUniqueParameterId(): int;
}
