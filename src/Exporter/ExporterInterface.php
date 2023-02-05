<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Exporter;

use Kreyu\Bundle\DataTableBundle\DataTableView;
use Symfony\Component\HttpFoundation\File\File;

interface ExporterInterface
{
    public function getName(): string;

    public function export(DataTableView $view): File;
}
