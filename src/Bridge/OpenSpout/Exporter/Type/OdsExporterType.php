<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\OpenSpout\Exporter\Type;

use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\ODS;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OdsExporterType extends AbstractOpenSpoutExporterType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'default_row_style' => new Style(),
                'should_create_new_sheets_automatically' => true,
                'default_column_width' => null,
                'default_row_height' => null,
            ])
            ->setAllowedTypes('default_row_style', Style::class)
            ->setAllowedTypes('should_create_new_sheets_automatically', 'bool')
            ->setAllowedTypes('default_column_width', ['null', 'float'])
            ->setAllowedTypes('default_row_height', ['null', 'float'])
        ;
    }

    protected function getWriterClass(): string
    {
        return ODS\Writer::class;
    }

    protected function getWriterOptions(array $options): ODS\Options
    {
        $writerOptions = new ODS\Options();
        $writerOptions->DEFAULT_ROW_STYLE = $options['default_row_style'];
        $writerOptions->SHOULD_CREATE_NEW_SHEETS_AUTOMATICALLY = $options['should_create_new_sheets_automatically'];
        $writerOptions->DEFAULT_COLUMN_WIDTH = $options['default_column_width'];
        $writerOptions->DEFAULT_ROW_HEIGHT = $options['default_row_height'];

        return $writerOptions;
    }

    protected function getExtension(): string
    {
        return 'ods';
    }
}
