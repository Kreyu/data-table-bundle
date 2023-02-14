<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\PhpSpreadsheet\Exporter\Type;

use PhpOffice\PhpSpreadsheet\Shared\StringHelper;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Writer\IWriter;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CsvType extends AbstractType
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
            ->setAllowedTypes('use_bom', 'string')
            ->setAllowedTypes('include_separator_line', 'bool')
            ->setAllowedTypes('excel_compatibility', 'bool')
            ->setAllowedTypes('output_encoding', 'string')
            ->setAllowedTypes('decimal_separator', 'string')
            ->setAllowedTypes('thousands_separator', 'string')
        ;
    }

    protected function getWriter(Spreadsheet $spreadsheet, array $options): IWriter
    {
        StringHelper::setDecimalSeparator($options['decimal_separator']);
        StringHelper::setThousandsSeparator($options['thousands_separator']);

        return (new Csv($spreadsheet))
            ->setDelimiter($options['delimiter'])
            ->setEnclosure($options['enclosure'])
            ->setEnclosureRequired($options['enclosure_required'])
            ->setLineEnding($options['line_ending'])
            ->setSheetIndex($options['sheet_index'])
            ->setUseBOM($options['use_bom'])
            ->setIncludeSeparatorLine($options['include_separator_line'])
            ->setExcelCompatibility($options['excel_compatibility'])
            ->setOutputEncoding($options['output_encoding'])
        ;
    }
}
