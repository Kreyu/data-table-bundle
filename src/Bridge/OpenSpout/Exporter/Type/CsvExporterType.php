<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\OpenSpout\Exporter\Type;

use OpenSpout\Writer\CSV;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CsvExporterType extends AbstractOpenSpoutExporterType
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
        /** @var string $fieldDelimiter */
        $fieldDelimiter = $options['field_delimiter'];

        /** @var string $fieldEnclosure */
        $fieldEnclosure = $options['field_enclosure'];

        /** @var bool $shouldAddBom */
        $shouldAddBom = $options['should_add_bom'];

        /** @var positive-int $flushThreshold */
        $flushThreshold = $options['flush_threshold'];

        $writerOptions = new CSV\Options();
        $writerOptions->FIELD_DELIMITER = $fieldDelimiter;
        $writerOptions->FIELD_ENCLOSURE = $fieldEnclosure;
        $writerOptions->SHOULD_ADD_BOM = $shouldAddBom;
        $writerOptions->FLUSH_THRESHOLD = $flushThreshold;

        return $writerOptions;
    }

    protected function getExtension(): string
    {
        return 'csv';
    }
}
