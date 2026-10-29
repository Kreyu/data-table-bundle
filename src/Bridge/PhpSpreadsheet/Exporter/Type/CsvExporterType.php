<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\PhpSpreadsheet\Exporter\Type;

use PhpOffice\PhpSpreadsheet\Shared\StringHelper;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Writer\IWriter;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CsvExporterType extends AbstractPhpSpreadsheetExporterType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'delimiter' => ',',
                'enclosure' => '"',
                'enclosure_required' => true,
                'line_ending' => PHP_EOL,
                'sheet_index' => 0,
                'use_bom' => false,
                'include_separator_line' => false,
                'excel_compatibility' => false,
                'output_encoding' => '',
                'decimal_separator' => StringHelper::getDecimalSeparator(),
                'thousands_separator' => StringHelper::getThousandsSeparator(),
            ])
            ->setAllowedTypes('delimiter', 'string')
            ->setAllowedTypes('enclosure', 'string')
            ->setAllowedTypes('enclosure_required', 'bool')
            ->setAllowedTypes('line_ending', 'string')
            ->setAllowedTypes('sheet_index', 'int')
            ->setAllowedTypes('use_bom', 'bool')
            ->setAllowedTypes('include_separator_line', 'bool')
            ->setAllowedTypes('excel_compatibility', 'bool')
            ->setAllowedTypes('output_encoding', 'string')
            ->setAllowedTypes('decimal_separator', 'string')
            ->setAllowedTypes('thousands_separator', 'string')
        ;
    }

    protected function getWriter(Spreadsheet $spreadsheet, array $options): IWriter
    {
        /** @var string $decimalSeparator */
        $decimalSeparator = $options['decimal_separator'];

        /** @var string $thousandsSeparator */
        $thousandsSeparator = $options['thousands_separator'];

        /** @var string $delimiter */
        $delimiter = $options['delimiter'];

        /** @var string $enclosure */
        $enclosure = $options['enclosure'];

        /** @var bool $enclosureRequired */
        $enclosureRequired = $options['enclosure_required'];

        /** @var string $lineEnding */
        $lineEnding = $options['line_ending'];

        /** @var int $sheetIndex */
        $sheetIndex = $options['sheet_index'];

        /** @var bool $useBom */
        $useBom = $options['use_bom'];

        /** @var bool $includeSeparatorLine */
        $includeSeparatorLine = $options['include_separator_line'];

        /** @var bool $excelCompatibility */
        $excelCompatibility = $options['excel_compatibility'];

        /** @var string $outputEncoding */
        $outputEncoding = $options['output_encoding'];

        StringHelper::setDecimalSeparator($decimalSeparator);
        StringHelper::setThousandsSeparator($thousandsSeparator);

        return (new Csv($spreadsheet))
            ->setDelimiter($delimiter)
            ->setEnclosure($enclosure)
            ->setEnclosureRequired($enclosureRequired)
            ->setLineEnding($lineEnding)
            ->setSheetIndex($sheetIndex)
            ->setUseBOM($useBom)
            ->setIncludeSeparatorLine($includeSeparatorLine)
            ->setExcelCompatibility($excelCompatibility)
            ->setOutputEncoding($outputEncoding)
        ;
    }
}
