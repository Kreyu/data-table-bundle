<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter\Event;

final class FilterEvents
{
    public const PRE_HANDLE = 'kreyu_data_table.filter.pre_handle';

    public const POST_HANDLE = 'kreyu_data_table.filter.post_handle';

    private function __construct()
    {
    }
}
