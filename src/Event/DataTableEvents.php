<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Event;

use Kreyu\Bundle\DataTableBundle\DataTableInterface;

final class DataTableEvents
{
    /**
     * @see DataTableInterface::paginate()
     */
    public const PRE_PAGINATE = 'kreyu_data_table.pre_paginate';

    /**
     * @see DataTableInterface::paginate()
     */
    public const POST_PAGINATE = 'kreyu_data_table.post_paginate';

    /**
     * @see DataTableInterface::sort()
     */
    public const PRE_SORT = 'kreyu_data_table.pre_sort';

    /**
     * @see DataTableInterface::sort()
     */
    public const POST_SORT = 'kreyu_data_table.post_sort';

    /**
     * @see DataTableInterface::filter()
     */
    public const PRE_FILTER = 'kreyu_data_table.pre_filter';

    /**
     * @see DataTableInterface::filter()
     */
    public const POST_FILTER = 'kreyu_data_table.post_filter';

    /**
     * @see DataTableInterface::personalize()
     */
    public const PRE_PERSONALIZE = 'kreyu_data_table.pre_personalize';

    /**
     * @see DataTableInterface::personalize()
     */
    public const POST_PERSONALIZE = 'kreyu_data_table.post_personalize';

    /**
     * @see DataTableInterface::export()
     */
    public const PRE_EXPORT = 'kreyu_data_table.pre_export';

    private function __construct()
    {
    }
}
