<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type;

use Kreyu\Bundle\DataTableBundle\Filter\Type\AbstractFilterType;

abstract class AbstractDoctrineOrmFilterType extends AbstractFilterType
{
    public function getParent(): ?string
    {
        return DoctrineOrmFilterType::class;
    }
}
