<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Exporter\Type;

use Kreyu\Bundle\DataTableBundle\DataTableView;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\OptionsResolver\OptionsResolver;

interface ExporterTypeInterface
{
    public function export(DataTableView $view, array $options = []): File;

    public function configureOptions(OptionsResolver $resolver): void;

    /**
     * @return class-string<ExporterTypeInterface>|null
     */
    public function getParent(): ?string;
}
