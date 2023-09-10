<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Exporter;

use Kreyu\Bundle\DataTableBundle\DataTableInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExportData
{
    public ?string $filename = null;
    public ?string $exporter = null;
    public ExportStrategy $strategy = ExportStrategy::IncludeAll;
    public bool $includePersonalization = true;

    public static function fromArray(array $data): self
    {
        $resolver = (new OptionsResolver())
            ->setDefaults([
                'filename' => null,
                'exporter' => null,
                'strategy' => ExportStrategy::IncludeCurrentPage,
                'include_personalization' => true,
            ])
            ->setAllowedTypes('exporter', ['null', 'string', ExporterInterface::class])
            ->setAllowedTypes('filename', ['null', 'string'])
            ->setAllowedTypes('strategy', ['string', ExportStrategy::class])
            ->setAllowedTypes('include_personalization', 'bool')
            ->addNormalizer('strategy', function (Options $options, $value): ExportStrategy {
                return $value instanceof ExportStrategy ? $value : ExportStrategy::from($value);
            })
            ->addNormalizer('exporter', function (Options $options, $value): ?string {
                if ($value instanceof ExporterInterface) {
                    return $value->getName();
                }

                return $value;
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
        $self = new self();
        $self->filename = $dataTable->getConfig()->getName();

        return $self;
    }
}
