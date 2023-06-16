<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\PhpSpreadsheet\Exporter\Type;

use Kreyu\Bundle\DataTableBundle\Column\ColumnHeaderView;
use Kreyu\Bundle\DataTableBundle\Column\ColumnValueView;
use Kreyu\Bundle\DataTableBundle\DataTableView;
use Kreyu\Bundle\DataTableBundle\Exporter\ExportFile;
use Kreyu\Bundle\DataTableBundle\Exporter\Type\AbstractExporterType as BaseAbstractType;
use Kreyu\Bundle\DataTableBundle\HeaderRowView;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\IWriter;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class AbstractExporterType extends BaseAbstractType
{
    public function __construct(
        private ?TranslatorInterface $translator = null,
    ) {
    }

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
            /** @var HeaderRowView $headerRow */
            $headerRow = $view->vars['header_row'];

            $headers = array_filter($headerRow->children, function (ColumnHeaderView $view) {
                return false !== $view->vars['export'];
            });

            $this->appendRow(
                $worksheet,
                array_map(function (ColumnHeaderView $view) {
                    $label = $view->vars['export']['label'];

                    if ($this->translator && $translationDomain = $view->vars['export']['translation_domain'] ?? null) {
                        $label = $this->translator->trans($label, $view->vars['export']['translation_parameters'] ?? [], $translationDomain);
                    }

                    return $label;
                }, $headers),
            );
        }

        foreach ($view->vars['value_rows'] as $valueRow) {
            $values = array_filter($valueRow->children, function (ColumnValueView $view) {
                return false !== $view->vars['export'];
            });

            $this->appendRow(
                $worksheet,
                array_map(function (ColumnValueView $view) {
                    $value = $view->vars['export']['value'];

                    if (is_bool($value)) {
                        $value = (int) $value;
                    }

                    return $value;
                }, $values),
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
        return PhpSpreadsheetExporterType::class;
    }
}
