<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Exporter\Extension;

use Kreyu\Bundle\DataTableBundle\Exporter\Extension\AbstractExporterTypeExtension;
use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Exporter\Type\SimpleExporterType;

class SimpleExporterTypeFooExtension extends AbstractExporterTypeExtension
{
    public static function getExtendedTypes(): iterable
    {
        yield SimpleExporterType::class;
    }
}
