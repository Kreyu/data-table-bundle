<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Exporter;

use Kreyu\Bundle\DataTableBundle\DataTableInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExportData
{
    public string $filename;
    public ExporterInterface $exporter;
    public ExportStrategy $strategy = ExportStrategy::INCLUDE_ALL;
    public bool $includePersonalization = true;

    public static function fromArray(array $data): self
    {
        $resolver = (new OptionsResolver())
            ->setRequired('exporter')
            ->setDefaults([
                'filename' => 'export',
                'strategy' => ExportStrategy::INCLUDE_CURRENT_PAGE,
                'include_personalization' => false,
            ])
            ->setAllowedTypes('exporter', ExporterInterface::class)
            ->setAllowedTypes('filename', 'string')
            ->setAllowedTypes('strategy', ['string', ExportStrategy::class])
            ->setAllowedTypes('include_personalization', 'bool')
            ->setNormalizer('strategy', function (Options $options, $value): ExportStrategy {
                return $value instanceof ExportStrategy ? $value : ExportStrategy::from($value);
            })
        ;

        $data = array_intersect_key($data, array_flip($resolver->getDefinedOptions()));

        $data = $resolver->resolve($data);

        $self = new self();
        $self->filename = $data['filename'];
        $self->exporter = $data['exporter'];
        $self->strategy = $data['strategy'];
        $self->includePersonalization = $data['include_personalization'];

        return $self;
    }

    public static function fromDataTable(DataTableInterface $dataTable): self
    {
        $exporters = $dataTable->getConfig()->getExporters();

        if (empty($exporters)) {
            throw new \LogicException('Unable to create export data from data table without exporters');
        }

        $self = new self();
        $self->filename = $dataTable->getConfig()->getName();
        $self->exporter = $exporters[array_key_first($exporters)];

        return $self;
    }
}
