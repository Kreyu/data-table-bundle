<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\PhpSpreadsheet\Exporter\Type;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\IWriter;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class XlsxType extends AbstractType
{
    protected function getWriter(Spreadsheet $spreadsheet): IWriter
    {
        return new Xlsx($spreadsheet);
    }
}
