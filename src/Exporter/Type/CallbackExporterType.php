<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Exporter\Type;

use Kreyu\Bundle\DataTableBundle\DataTableView;
use Kreyu\Bundle\DataTableBundle\Exporter\ExporterInterface;
use Kreyu\Bundle\DataTableBundle\Exporter\ExportFile;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CallbackExporterType extends AbstractExporterType
{
    public function export(DataTableView $view, ExporterInterface $exporter, string $filename, array $options = []): ExportFile
    {
        return $options['callback']($view, $exporter, $filename, $options);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired('callback')
            ->setAllowedTypes('callback', ['callable'])
        ;
    }
}
