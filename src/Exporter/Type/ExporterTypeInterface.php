<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Exporter\Type;

use Kreyu\Bundle\DataTableBundle\DataTableView;
use Kreyu\Bundle\DataTableBundle\Exporter\ExportFile;
use Symfony\Component\OptionsResolver\OptionsResolver;

interface ExporterTypeInterface
{
    public function export(DataTableView $view, string $filename, array $options = []): ExportFile;

    public function configureOptions(OptionsResolver $resolver): void;

    /**
     * @return class-string<ExporterTypeInterface>|null
     */
    public function getParent(): ?string;
}
