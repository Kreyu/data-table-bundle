<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Extension\HttpFoundation;

use Kreyu\Bundle\DataTableBundle\Extension\AbstractDataTableExtension;

class HttpFoundationDataTableExtension extends AbstractDataTableExtension
{
    protected function loadTypeExtensions(): array
    {
        return [
            new HttpFoundationDataTableTypeExtension(),
        ];
    }
}
