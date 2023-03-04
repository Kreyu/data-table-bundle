<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\PhpSpreadsheet\Exporter\Type;

use Kreyu\Bundle\DataTableBundle\Column\ColumnView;
use Kreyu\Bundle\DataTableBundle\DataTableView;
use Kreyu\Bundle\DataTableBundle\Exporter\ExportFile;
use Kreyu\Bundle\DataTableBundle\Exporter\Type\AbstractType as BaseAbstractType;
use Kreyu\Bundle\DataTableBundle\HeadersRowView;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\IWriter;

abstract class AbstractType extends BaseAbstractType
{
    abstract protected function getWriter(Spreadsheet $spreadsheet, array $options): IWriter;

    /**
     * @throws Exception
     */
    public function export(DataTableView $view, string $filename, array $options = []): ExportFile
    {
        $spreadsheet = $this->createSpreadsheet($view, $options);

        $writer = $this->getWriter($spreadsheet, $options);
        $writer->setPreCalculateFormulas($options['pre_calculate_formulas']);

        $writer->save($tempnam = $this->getTempnam($options));

        $extension = mb_strtolower((new \ReflectionClass($writer))->getShortName());

        return new ExportFile($tempnam, "$filename.$extension");
    }

    /**
     * @throws Exception
     */
    protected function createSpreadsheet(DataTableView $view, array $options = []): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();

        $worksheet = $spreadsheet->getActiveSheet();

        if ($options['use_headers']) {
            /** @var HeadersRowView $headersRow */
            $headersRow = $view->vars['headers_row'];

            $columns = array_filter($headersRow->vars['columns'], function (ColumnView $view) {
                return $view->vars['export'];
            });

            $this->appendRow(
                $worksheet,
                array_map(function (ColumnView $column) {
                    return $column->vars['export_options']['label'];
                }, $columns),
            );
        }

        foreach ($view->vars['values_rows'] as $valuesRow) {
            $columns = array_filter($valuesRow->vars['columns'], function (ColumnView $view) {
                return $view->vars['export'];
            });

            $this->appendRow(
                $worksheet,
                array_map(function (ColumnView $column) {
                    return $column->vars['export_options']['value'];
                }, $columns),
            );
        }

        return $spreadsheet;
    }

    /**
     * @throws Exception
     */
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

    public function getParent(): ?string
    {
        return PhpSpreadsheetType::class;
    }
}
