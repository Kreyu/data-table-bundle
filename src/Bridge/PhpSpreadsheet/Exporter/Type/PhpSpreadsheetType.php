<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\PhpSpreadsheet\Exporter\Type;

use Kreyu\Bundle\DataTableBundle\DataTableView;
use Kreyu\Bundle\DataTableBundle\Exporter\Type\ExporterType as BaseExporterType;
use Kreyu\Bundle\DataTableBundle\Exporter\Type\ExporterTypeInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class PhpSpreadsheetType implements ExporterTypeInterface
{
    public function export(DataTableView $view, array $options = []): File
    {
        throw new \LogicException('Base exporter type cannot be called directly');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'pre_calculate_formulas' => true,
            ])
            ->setAllowedTypes('pre_calculate_formulas', 'bool')
        ;
    }

    public function getParent(): ?string
    {
        return BaseExporterType::class;
    }
}