<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\OpenSpout\Exporter\Type;

use OpenSpout\Writer\CSV\Options;
use OpenSpout\Writer\CSV\Writer;
use OpenSpout\Writer\WriterInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CsvExporterType extends AbstractExporterType
{
    protected function getExtension(): string
    {
        return 'csv';
    }

    protected function getWriter(array $options): WriterInterface
    {
        $writerOptions = new Options();
        $writerOptions->FIELD_DELIMITER = $options['field_delimiter'];
        $writerOptions->FIELD_ENCLOSURE = $options['field_enclosure'];
        $writerOptions->SHOULD_ADD_BOM = $options['should_add_bom'];
        $writerOptions->FLUSH_THRESHOLD = $options['flush_threshold'];

        return new Writer($writerOptions);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $options = new Options();

        $resolver
            ->setDefaults([
                'field_delimiter' => $options->FIELD_DELIMITER,
                'field_enclosure' => $options->FIELD_ENCLOSURE,
                'should_add_bom' => $options->SHOULD_ADD_BOM,
                'flush_threshold' => $options->FLUSH_THRESHOLD,
            ])
            ->setAllowedTypes('field_delimiter', 'string')
            ->setAllowedTypes('field_enclosure', 'string')
            ->setAllowedTypes('should_add_bom', 'bool')
            ->setAllowedTypes('flush_threshold', 'int')
        ;
    }
}
