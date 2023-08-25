<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\PhpSpreadsheet\Exporter\Type;

use Kreyu\Bundle\DataTableBundle\DataTableView;
use Kreyu\Bundle\DataTableBundle\Exporter\ExportFile;
use Kreyu\Bundle\DataTableBundle\Exporter\Type\AbstractExporterType;
use Kreyu\Bundle\DataTableBundle\Exporter\Type\ExporterType as BaseExporterType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class PhpSpreadsheetExporterType extends AbstractExporterType
{
    public function export(DataTableView $view, string $filename, array $options = []): ExportFile
    {
        throw new \LogicException('Base exporter type cannot be called directly');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        if (!class_exists(Spreadsheet::class)) {
            throw new \LogicException('Trying to use exporter that requires PhpSpreadsheet which is not installed. Try running "composer require phpoffice/phpspreadsheet".');
        }

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
