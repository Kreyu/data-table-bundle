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

    /**
     * @param array<string, mixed> $data
     */
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
            ->addNormalizer('strategy', static fn (
                Options $options,
                ExportStrategy|string $value,
            ): ExportStrategy => $value instanceof ExportStrategy ? $value : ExportStrategy::from($value))
            ->addNormalizer('exporter', static function (
                Options $options,
                ExporterInterface|string|null $value,
            ): ?string {
                if ($value instanceof ExporterInterface) {
                    return $value->getName();
                }

                return $value;
            })
        ;

        $data = array_intersect_key($data, array_flip($resolver->getDefinedOptions()));

        /** @var array{filename: ?string, exporter: ?string, strategy: ExportStrategy, include_personalization: bool} $resolvedData */
        $resolvedData = $resolver->resolve($data);

        $self = new self();
        $self->filename = $resolvedData['filename'];
        $self->exporter = $resolvedData['exporter'];
        $self->strategy = $resolvedData['strategy'];
        $self->includePersonalization = $resolvedData['include_personalization'];

        return $self;
    }

    public static function fromDataTable(DataTableInterface $dataTable): self
    {
        $self = new self();
        $self->filename = $dataTable->getConfig()->getName();

        return $self;
    }
}
