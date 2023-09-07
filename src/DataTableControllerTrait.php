<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle;

/**
 * @deprecated since 0.9.3, use {@see DataTableFactoryAwareTrait} instead
 */
trait DataTableControllerTrait
{
    use DataTableFactoryAwareTrait;
}