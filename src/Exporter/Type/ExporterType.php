<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Exporter\Type;

use Kreyu\Bundle\DataTableBundle\DataTableView;
use Kreyu\Bundle\DataTableBundle\Exception\LogicException;
use Kreyu\Bundle\DataTableBundle\Exporter\ExporterBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Exporter\ExportFile;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ExporterType implements ExporterTypeInterface
{
    public function export(DataTableView $view, string $filename, array $options = []): ExportFile
    {
        throw new LogicException('Base exporter type cannot be called directly');
    }

    public function buildExporter(ExporterBuilderInterface $builder, array $options): void
    {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'use_headers' => true,
            'label' => null,
            'tempnam_dir' => '/tmp',
            'tempnam_prefix' => 'exporter_',
        ]);
    }

    public function getName(): string
    {
        return 'exporter';
    }

    public function getParent(): ?string
    {
        return null;
    }
}
