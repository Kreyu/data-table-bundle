<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Responsive;

enum Device: string
{
    case Phone = 'phone';
    case Tablet = 'tablet';
    case Desktop = 'desktop';
}
