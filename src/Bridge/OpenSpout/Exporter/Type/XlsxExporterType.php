<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\OpenSpout\Exporter\Type;

use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\WriterInterface;
use OpenSpout\Writer\XLSX\Options;
use OpenSpout\Writer\XLSX\Writer;
use Symfony\Component\OptionsResolver\OptionsResolver;

class XlsxExporterType extends AbstractExporterType
{
    protected function getExtension(): string
    {
        return 'xlsx';
    }

    protected function getWriter(array $options): WriterInterface
    {
        $writerOptions = new Options();
        $writerOptions->DEFAULT_ROW_STYLE = $options['default_row_style'];
        $writerOptions->SHOULD_CREATE_NEW_SHEETS_AUTOMATICALLY = $options['should_create_new_sheets_automatically'];
        $writerOptions->DEFAULT_COLUMN_WIDTH = $options['default_column_width'];
        $writerOptions->DEFAULT_ROW_HEIGHT = $options['default_row_height'];

        return new Writer($writerOptions);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $options = new Options();

        $resolver
            ->setDefaults([
                'default_row_style' => $options->DEFAULT_ROW_STYLE,
                'should_create_new_sheets_automatically' => $options->SHOULD_CREATE_NEW_SHEETS_AUTOMATICALLY,
                'default_column_width' => $options->DEFAULT_COLUMN_WIDTH,
                'default_row_height' => $options->DEFAULT_ROW_HEIGHT,
            ])
            ->setAllowedTypes('default_row_style', Style::class)
            ->setAllowedTypes('should_create_new_sheets_automatically', 'bool')
            ->setAllowedTypes('default_column_width', ['null', 'float'])
            ->setAllowedTypes('default_row_height', ['null', 'float'])
        ;
    }
}
