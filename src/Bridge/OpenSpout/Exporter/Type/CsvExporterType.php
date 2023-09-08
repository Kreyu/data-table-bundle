<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\OpenSpout\Exporter\Type;

use OpenSpout\Writer\CSV;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CsvExporterType extends AbstractOpenSpoutExporterType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'field_delimiter' => ',',
                'field_enclosure' => '"',
                'should_add_bom' => true,
                'flush_threshold' => 500,
            ])
            ->setAllowedTypes('field_delimiter', 'string')
            ->setAllowedTypes('field_enclosure', 'string')
            ->setAllowedTypes('should_add_bom', 'bool')
            ->setAllowedTypes('flush_threshold', 'int')
        ;
    }

    protected function getWriterClass(): string
    {
        return CSV\Writer::class;
    }

    protected function getWriterOptions(array $options): CSV\Options
    {
        $writerOptions = new CSV\Options();
        $writerOptions->FIELD_DELIMITER = $options['field_delimiter'];
        $writerOptions->FIELD_ENCLOSURE = $options['field_enclosure'];
        $writerOptions->SHOULD_ADD_BOM = $options['should_add_bom'];
        $writerOptions->FLUSH_THRESHOLD = $options['flush_threshold'];

        return $writerOptions;
    }

    protected function getExtension(): string
    {
        return 'csv';
    }
}
