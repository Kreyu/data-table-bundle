<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\PhpSpreadsheet\Exporter\Type;

use Kreyu\Bundle\DataTableBundle\Column\ColumnHeaderView;
use Kreyu\Bundle\DataTableBundle\Column\ColumnValueView;
use Kreyu\Bundle\DataTableBundle\DataTableView;
use Kreyu\Bundle\DataTableBundle\Exception\UnexpectedTypeException;
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
     * @param array<string, mixed> $options
     *
     * @throws Exception
     */
    public function export(
        DataTableView $view,
        ExporterInterface $exporter,
        string $filename,
        array $options = [],
    ): ExportFile {
        /** @var bool $preCalculateFormulas */
        $preCalculateFormulas = $options['pre_calculate_formulas'];

        $spreadsheet = $this->createSpreadsheet($view, $options);

        $writer = $this->getWriter($spreadsheet, $options);
        $writer->setPreCalculateFormulas($preCalculateFormulas);

        $writer->save($tempnam = $this->getTempnam($options));

        $extension = mb_strtolower((new \ReflectionClass($writer))->getShortName());

        return new ExportFile($tempnam, "{$filename}.{$extension}");
    }

    /**
     * @throws Exception
     */
    protected function createSpreadsheet(DataTableView $view, array $options = []): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();

        $worksheet = $spreadsheet->getActiveSheet();

        /** @var bool $useHeaders */
        $useHeaders = $options['use_headers'];
        $headerRow = $useHeaders ? $view->headerRow : null;

        if ($headerRow !== null) {
            $this->appendRow($worksheet, array_map(
                static fn (ColumnHeaderView $view) => $view->vars['label'],
                $headerRow->children,
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
     * @param array<array-key, mixed> $data
     *
     * @throws Exception
     */
    protected function appendRow(Worksheet $worksheet, array $data): void
    {
        $row = $worksheet->getHighestRow();

        $worksheet->insertNewRowBefore($row);

        $index = 0;

        /** @var mixed $value */
        foreach ($data as $value) {
            $worksheet->setCellValue([++$index, $row], $this->getCellValue($value));
        }
    }

    public function getParent(): ?string
    {
        return PhpSpreadsheetExporterType::class;
    }

    private function getCellValue(mixed $value): mixed
    {
        $isArray = is_array($value);

        if (!$isArray) {
            return $value;
        }

        return implode(', ', array_map($this->getImplodableValue(...), $value));
    }

    private function getImplodableValue(mixed $value): \Stringable|bool|float|int|string|null
    {
        if ($value instanceof \Stringable) {
            return $value;
        }

        if ($value === null) {
            return null;
        }

        $isScalar = is_scalar($value);

        if ($isScalar) {
            return $value;
        }

        throw new UnexpectedTypeException($value, 'scalar, \Stringable or null');
    }
}
