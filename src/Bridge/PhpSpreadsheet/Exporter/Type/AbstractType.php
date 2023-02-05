<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\PhpSpreadsheet\Exporter\Type;

use Kreyu\Bundle\DataTableBundle\Column\ColumnView;
use Kreyu\Bundle\DataTableBundle\DataTableView;
use Kreyu\Bundle\DataTableBundle\Exporter\Type\AbstractType as BaseAbstractType;
use Kreyu\Bundle\DataTableBundle\HeadersRowView;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\IWriter;
use Symfony\Component\HttpFoundation\File\File;

abstract class AbstractType extends BaseAbstractType
{
    abstract protected function getWriter(Spreadsheet $spreadsheet): IWriter;

    public function export(DataTableView $view, array $options = []): File
    {
        $spreadsheet = $this->createSpreadsheet($view, $options);

        $this->getWriter($spreadsheet)->save($tempnam = $this->getTempnam($options));

        return new File($tempnam);
    }

    protected function createSpreadsheet(DataTableView $view, array $options = []): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();

        $worksheet = $spreadsheet->getActiveSheet();

        if ($options['use_headers']) {
            /** @var HeadersRowView $headersRow */
            $headersRow = $view->vars['headers_row'];

            $this->appendRow(
                $worksheet,
                array_map(function (ColumnView $column) {
                    return $column->vars['label'];
                }, $headersRow->vars['columns'])
            );
        }

        foreach ($view->vars['values_rows'] as $valuesRow) {
            $this->appendRow(
                $worksheet,
                array_map(function (ColumnView $column) {
                    return $column->vars['value'];
                }, $valuesRow->vars['columns'])
            );
        }

        return $spreadsheet;
    }

    protected function appendRow(Worksheet $worksheet, array $data): void
    {
        $row = $worksheet->getHighestRow();

        $worksheet->insertNewRowBefore($row);

        $index = 0;

        foreach ($data as $value) {
            if (is_array($value)) {
                $value = implode(', ', $value);
            }

            $worksheet->setCellValue([++$index, $row], $value);
        }
    }
}