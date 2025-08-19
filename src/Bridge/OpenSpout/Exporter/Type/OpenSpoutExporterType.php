<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\OpenSpout\Exporter\Type;

use Kreyu\Bundle\DataTableBundle\DataTableView;
use Kreyu\Bundle\DataTableBundle\Exception\LogicException;
use Kreyu\Bundle\DataTableBundle\Exporter\ExporterInterface;
use Kreyu\Bundle\DataTableBundle\Exporter\ExportFile;
use Kreyu\Bundle\DataTableBundle\Exporter\Type\AbstractExporterType;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\WriterInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class OpenSpoutExporterType extends AbstractExporterType
{
    public function export(DataTableView $view, ExporterInterface $exporter, string $filename, array $options = []): ExportFile
    {
        throw new LogicException('Base OpenSpout exporter type cannot be called directly');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        if (!interface_exists(WriterInterface::class)) {
            throw new LogicException('Trying to use exporter that requires OpenSpout which is not installed. Try running "composer require openspout/openspout".');
        }

        $resolver
            ->setDefaults([
                'header_row_style' => null,
                'header_cell_style' => null,
                'value_row_style' => null,
                'value_cell_style' => null,
            ])
            ->setAllowedTypes('header_row_style', ['null', \Closure::class, Style::class])
            ->setAllowedTypes('header_cell_style', ['null', \Closure::class, Style::class])
            ->setAllowedTypes('value_row_style', ['null', \Closure::class, Style::class])
            ->setAllowedTypes('value_cell_style', ['null', \Closure::class, Style::class])
        ;
    }
}
