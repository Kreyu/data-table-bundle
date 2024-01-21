<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Event;

final class DoctrineOrmFilterEvents
{
    public const PRE_SET_PARAMETERS = 'kreyu_data_table.doctrine_orm.filter.pre_set_parameters';

    public const PRE_APPLY_EXPRESSION = 'kreyu_data_table.doctrine_orm.filter.pre_apply_expression';

    /**
     * @codeCoverageIgnore
     */
    private function __construct()
    {
    }
}
