<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Sorting;

enum Direction: string
{
    case Asc = 'asc';
    case Desc = 'desc';
}
