<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\PhpSpreadsheet\Exporter\Type;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\IWriter;
use PhpOffice\PhpSpreadsheet\Writer\Ods;

class OdsExporterType extends AbstractPhpSpreadsheetExporterType
{
    protected function getWriter(Spreadsheet $spreadsheet, array $options): IWriter
    {
        return new Ods($spreadsheet);
    }
}
