<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\PhpSpreadsheet\Exporter\Type;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\IWriter;
use PhpOffice\PhpSpreadsheet\Writer\Xls;

class XlsExporterType extends AbstractExporterType
{
    protected function getWriter(Spreadsheet $spreadsheet, array $options): IWriter
    {
        return new Xls($spreadsheet);
    }
}
