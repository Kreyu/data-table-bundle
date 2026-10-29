<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\OpenSpout\Exporter\Type;

use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\XLSX;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class XlsxExporterType extends AbstractOpenSpoutExporterType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'default_row_style' => new Style(),
                'should_create_new_sheets_automatically' => true,
                'should_use_inline_strings' => true,
                'default_column_width' => null,
                'default_row_height' => null,
            ])
            ->setAllowedTypes('default_row_style', Style::class)
            ->setAllowedTypes('should_create_new_sheets_automatically', 'bool')
            ->setAllowedTypes('should_use_inline_strings', 'bool')
            ->setAllowedTypes('default_column_width', ['null', 'float'])
            ->setAllowedTypes('default_row_height', ['null', 'float'])
        ;
    }

    protected function getWriterClass(): string
    {
        return XLSX\Writer::class;
    }

    protected function getWriterOptions(array $options): XLSX\Options
    {
        /** @var Style $defaultRowStyle */
        $defaultRowStyle = $options['default_row_style'];

        /** @var bool $shouldCreateNewSheetsAutomatically */
        $shouldCreateNewSheetsAutomatically = $options['should_create_new_sheets_automatically'];

        /** @var bool $shouldUseInlineStrings */
        $shouldUseInlineStrings = $options['should_use_inline_strings'];

        /** @var float|null $defaultColumnWidth */
        $defaultColumnWidth = $options['default_column_width'];

        /** @var float|null $defaultRowHeight */
        $defaultRowHeight = $options['default_row_height'];

        $writerOptions = new XLSX\Options();
        $writerOptions->DEFAULT_ROW_STYLE = $defaultRowStyle;
        $writerOptions->SHOULD_CREATE_NEW_SHEETS_AUTOMATICALLY = $shouldCreateNewSheetsAutomatically;
        $writerOptions->SHOULD_USE_INLINE_STRINGS = $shouldUseInlineStrings;
        $writerOptions->DEFAULT_COLUMN_WIDTH = $defaultColumnWidth;
        $writerOptions->DEFAULT_ROW_HEIGHT = $defaultRowHeight;

        return $writerOptions;
    }

    protected function getExtension(): string
    {
        return 'xlsx';
    }
}
