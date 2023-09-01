<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Action;

enum ActionContext: string
{
    case Global = 'global';
    case Batch = 'batch';
    case Row = 'row';
}
