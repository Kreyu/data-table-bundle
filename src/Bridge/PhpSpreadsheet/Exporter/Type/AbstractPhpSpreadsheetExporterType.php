<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\PhpSpreadsheet\Exporter\Type;

use Kreyu\Bundle\DataTableBundle\Column\ColumnHeaderView;
use Kreyu\Bundle\DataTableBundle\Column\ColumnValueView;
use Kreyu\Bundle\DataTableBundle\DataTableView;
use Kreyu\Bundle\DataTableBundle\Exporter\ExporterInterface;
use Kreyu\Bundle\DataTableBundle\Exporter\ExportFile;
use Kreyu\Bundle\DataTableBundle\Exporter\Type\AbstractExporterType;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\IWriter;

abstract class AbstractPhpSpreadsheetExporterType extends AbstractExporterType
{
    abstract protected function getWriter(Spreadsheet $spreadsheet, array $options): IWriter;

    /**
     * @throws Exception
     */
    public function export(DataTableView $view, ExporterInterface $exporter, string $filename, array $options = []): ExportFile
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
            $this->appendRow($worksheet, array_map(
                static fn (ColumnHeaderView $view) => $view->vars['label'],
                $view->headerRow->children,
            ));
        }

        foreach ($view->valueRows as $valueRow) {
            $this->appendRow($worksheet, array_map(
                static fn (ColumnValueView $view) => $view->vars['value'],
                $valueRow->children,
            ));
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
        return PhpSpreadsheetExporterType::class;
    }
}
